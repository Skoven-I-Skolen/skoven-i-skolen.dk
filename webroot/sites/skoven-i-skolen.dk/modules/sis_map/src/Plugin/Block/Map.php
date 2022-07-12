<?php

namespace Drupal\sis_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\sis_map\MapManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Map Block.
 *
 * @Block(
 *   id = "map_block",
 *   admin_label = @Translation("Map Block"),
 *   category = @Translation("Skoven-i-Skolen"),
 * )
 */
class Map extends BlockBase implements ContainerFactoryPluginInterface {

  /** @var MapManager $mapManager */
  protected $mapManager;

  protected function baseConfigurationDefaults(): array {
    return [
      'id' => $this->getPluginId(),
      'label' => t('Map Block'),
      'provider' => $this->pluginDefinition['provider'],
      'label_display' => BlockPluginInterface::BLOCK_LABEL_VISIBLE,
    ];
  }

  public function build() {
    $markers = $this->mapManager->loadMapMarkers();
    $filters = $this->mapManager->fetchFilters($markers);
    $icons = $this->mapManager->loadIcons($filters);
    return [
      '#type' => 'map',
      '#markers' => $markers,
      '#filters' => $filters,
      '#icons' => $icons,
    ];
  }

  public function __construct(array $configuration, $plugin_id, $plugin_definition, MapManager $mapManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->mapManager = $mapManager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('sis_map.manager'),
    );
  }

}
