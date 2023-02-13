<?php

namespace Drupal\sis_map;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\MetadataBubblingUrlGenerator;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

class MapManager {

  public const CONFIG_KEY = 'sis_map.settings';
  public const ADDRESS_FIELD_NAME = 'sis_map_address_dawa';
  public const LATITUDE_FIELD_NAME = 'lat';
  public const LONGITUDE_FIELD_NAME = 'lon';

  protected $configurationFactory;
  protected $entityFieldManager;
  protected $entityTypeManager;
  protected $urlGenerator;

  public function __construct(ConfigFactoryInterface $configurationFactory, EntityFieldManagerInterface $entityFieldManager, EntityTypeManagerInterface $entityTypeManager, MetadataBubblingUrlGenerator $urlGenerator) {
    $this->configurationFactory = $configurationFactory;
    $this->entityFieldManager = $entityFieldManager;
    $this->entityTypeManager = $entityTypeManager;
    $this->urlGenerator = $urlGenerator;
  }

  public function addGeolocationToContentBundle($bundle) {
    $address_field_storage = FieldStorageConfig::loadByName('node', self::ADDRESS_FIELD_NAME);
    $latitude_field_storage = FieldStorageConfig::loadByName('node', self::LATITUDE_FIELD_NAME);
    $longitude_field_storage = FieldStorageConfig::loadByName('node', self::LONGITUDE_FIELD_NAME);

    if (!$address_field_storage) {
      $address_field_storage = FieldStorageConfig::create([
        'field_name' => self::ADDRESS_FIELD_NAME,
        'entity_type' => 'node',
        'type' => 'dawa_address_autocomplete',
        'locked' => FALSE,
        'cardinality' => 1,
      ]);
      $address_field_storage->save();
    }
    $address_field_config = FieldConfig::loadByName('node', $bundle, $address_field_storage->getName());
    if (!$address_field_config) {
      $address_field_config = FieldConfig::create([
        'field_storage' => $address_field_storage,
        'bundle' => $bundle,
        'label' => t('Address', [], ['context' => 'sis_map']),
      ]);
      $address_field_config->save();
    }

    if (!$latitude_field_storage) {
      $latitude_field_storage = FieldStorageConfig::create([
        'field_name' => self::LATITUDE_FIELD_NAME,
        'entity_type' => 'node',
        'type' => 'string',
        'locked' => FALSE,
        'cardinality' => 1,
      ]);
      $latitude_field_storage->save();
    }
    $latitude_field_config = FieldConfig::loadByName('node', $bundle, $latitude_field_storage->getName());
    if (!$latitude_field_config) {
      $latitude_field_config = FieldConfig::create([
        'field_storage' => $latitude_field_storage,
        'bundle' => $bundle,
        'label' => t('Latitude', [], ['context' => 'sis_map']),
      ]);
      $latitude_field_config->save();
    }

    if (!$longitude_field_storage) {
      $longitude_field_storage = FieldStorageConfig::create([
        'field_name' => self::LONGITUDE_FIELD_NAME,
        'entity_type' => 'node',
        'type' => 'string',
        'locked' => FALSE,
        'cardinality' => 1,
      ]);
      $longitude_field_storage->save();
    }
    $longitude_field_config = FieldConfig::loadByName('node', $bundle, $longitude_field_storage->getName());
    if (!$longitude_field_config) {
      $longitude_field_config = FieldConfig::create([
        'field_storage' => $longitude_field_storage,
        'bundle' => $bundle,
        'label' => t('Longitude', [], ['context' => 'sis_map']),
      ]);
      $longitude_field_config->save();
    }
  }

  public function buildFilters($bundle): array  {
    $filters = [];

    $content_type_fields = $this->entityFieldManager->getFieldDefinitions('node', $bundle);
    foreach ($content_type_fields as $field) {
      if ($field->getType() === 'entity_reference') {
        if (($settings = $field->getSettings()) && ($settings['handler'] === 'default:taxonomy_term')) {
          if (!in_array($field->getName(), $filters, TRUE)) {
            $filters[] = $field->getName();
          }
        }
      }
    }
    return $filters;
  }

  public function fetchFilters($markers) {
    $filters = [];

    foreach ($markers as $marker) {
      foreach ($marker['filters'] as $key => $value) {
        if (!isset($filters[$key])) {
          $filters[$key] = [];
        }
        if (is_array($value)) {
          foreach ($value as $k => $v) {
            if (isset($filters[$key]) && !in_array($v, $filters[$key])) {
              $weight = random_int(200, 999);
              $term = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties(['name' => $v]);
              if ($term && $term = reset($term)) {
                $weight = $term->getWeight();
              }
              $filters[$key][$weight] = $v;
              ksort($filters[$key]);
            }
          }
        }
      }
    }

    return $filters;
  }

  public function loadIcons($filters): array {

    $icons = [];
    $all_filters = [];
    foreach ($filters as $key => $value) {
      $all_filters[] = $key;
      foreach ($value as $f) {
        $all_filters[] = $f;
      }
    }

    foreach ($all_filters as $filter) {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')
        ->loadByProperties(['name' => $filter]);
      if ($term = reset($term)) {
        if ($term->hasField('field_icon')) {
          $icon = $term->get('field_icon')->first();
          if ($icon !== NULL) {
            $name = explode(';', $icon->get('value')->getString())[1];
            $icons[$filter] = $name;
          }
        }
      }
    }

    return $icons;
  }

  public function loadMapMarkers(string $author_id = NULL): array {

    $enabled_filters = $this->configurationFactory
      ->get(self::CONFIG_KEY)
      ->get('enabled_filters');

    $enabled_content_types = $this->configurationFactory
      ->get(self::CONFIG_KEY)
      ->get('enabled_content_types');

    $map_marker_array = [];

    foreach ($enabled_content_types as $bundle) {
      $params = ['type' => $bundle, 'status' => 1];
      if ($author_id) {
        $params['uid'] = $author_id;
      }
      $all_nodes = $this->entityTypeManager
        ->getStorage('node')
        ->loadByProperties($params);
      foreach($all_nodes as $node) {
        $map_marker_filters = [];
        if ($node->hasField(self::ADDRESS_FIELD_NAME)) {
          foreach ($enabled_filters as $filter) {
            if ($node->hasField($filter)) {
              foreach ($node->get($filter)->getValue() as $value) {
                $term = $this->entityTypeManager->getStorage('taxonomy_term')
                  ->load($value['target_id']);
                if ($term) {
                  $value = $term->get('name')->value;
                  $vid = $term->get('vid')->getString();
                  $vocabulary_name = $this->entityTypeManager->getStorage('taxonomy_vocabulary')
                    ->load($vid)->get('name');
                  $map_marker_filters[$vocabulary_name][] = $value;
                }
              }
            }
          }
        }

        $url = $this->urlGenerator
          ->generateFromRoute('entity.node.canonical', ['node' => $node->get('nid')->getString()], ['absolute' => TRUE]);

        if (($node->get(self::LATITUDE_FIELD_NAME)->getString() && $node->get(self::LONGITUDE_FIELD_NAME)->getString()) ||
          $node->get(self::ADDRESS_FIELD_NAME)->getString()) {
          // Add marker to final array only if it has geographical data.
          $map_marker_array[$node->get('nid')->getString()] = [
            'node' => $node->toArray(),
            'filters' => $map_marker_filters,
            'address' => $node->get(self::ADDRESS_FIELD_NAME)->getString() ?? NULL,
            'lat' => $node->get(self::LATITUDE_FIELD_NAME)->getString() ?? NULL,
            'lon' => $node->get(self::LONGITUDE_FIELD_NAME)->getString() ?? NULL,
            'url' => $url,
          ];
        }
      }
    }

    return $map_marker_array;
  }

  public function removeGeolocationFromBundle($bundle) {
    $address_field_config = FieldConfig::loadByName('node', $bundle, self::ADDRESS_FIELD_NAME);
    $latitude_field_config = FieldConfig::loadByName('node', $bundle, self::LATITUDE_FIELD_NAME);
    $longitude_field_config = FieldConfig::loadByName('node', $bundle, self::LONGITUDE_FIELD_NAME);

    if ($address_field_config) {
      $address_field_config->delete();
    }

    if ($latitude_field_config) {
      $latitude_field_config->delete();
    }

    if ($longitude_field_config) {
      $longitude_field_config->delete();
    }
  }

}
