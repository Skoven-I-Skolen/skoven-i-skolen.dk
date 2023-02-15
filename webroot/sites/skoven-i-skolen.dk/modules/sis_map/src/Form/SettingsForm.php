<?php

namespace Drupal\sis_map\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\sis_map\MapManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Map Manager service.
   *
   * @var MapManager
   */
  protected $mapManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager, MapManager $mapManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->mapManager = $mapManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var ConfigFactoryInterface $config_factory */
    $entityTypeManager = $container->get('entity_type.manager');
    /** @var MapManager $config_factory */
    $mapManager = $container->get('sis_map.manager');
    return new static($entityTypeManager, $mapManager);
  }

  protected function getEditableConfigNames() {
    return MapManager::CONFIG_KEY;
  }

  public function getFormId() {
    return MapManager::CONFIG_KEY;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['general_settings_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('General settings'),
    ];

    $form['general_settings_fieldset']['display_map_helpers'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display map helpers'),
      '#default_value' => $this->configFactory()->get(MapManager::CONFIG_KEY)->get('display_map_helpers') ?? false,
    ];

    $form['general_settings_fieldset']['always_show_all_filters'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Always show all filters'),
      '#default_value' => $this->configFactory()->get(MapManager::CONFIG_KEY)->get('always_show_all_filters') ?? false,
    ];

    $form['general_settings_fieldset']['max_visible_filters'] = [
      '#type' => 'number',
      '#title' => $this->t('Max visible filters'),
      '#default_value' => $this->configFactory()->get(MapManager::CONFIG_KEY)->get('max_visible_filters') ?? 4,
    ];

    $form['enabled_content_types_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enabled content types'),
    ];

    $form['filters_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enabled filters'),
    ];

    $form['enabled_content_types_fieldset']['enabled_content_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enable geolocation on the following content types'),
      '#description' => $this->t('Warning: disabling geolocation on a content type will delete all map markers linked to it!'),
      '#options' => $this->getListOfContentTypes(),
      '#default_value' => $this->configFactory()->get(MapManager::CONFIG_KEY)->get('enabled_content_types') ?? [],
    ];

    $available_filters = $this->configFactory()
        ->get(MapManager::CONFIG_KEY)->get('available_filters') ?? [];
    $formatted_available_filters = [];
    foreach ($available_filters as $key => $value) {
      $formatted_available_filters[$value] = $value;
    }

    $form['filters_fieldset']['enabled_filters'] = [
      '#type' => 'select',
      '#title' => $this->t('Filters to be displayed on the map'),
      '#multiple' => TRUE,
      '#required' => FALSE,
      '#default_value' => $this->configFactory()
          ->get(MapManager::CONFIG_KEY)->get('enabled_filters') ?? [],
      '#options' => $formatted_available_filters,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $enabled_content_types = $form_state->getValue('enabled_content_types');
    $enabled_filters = $form_state->getValue('enabled_filters');

    foreach ($enabled_content_types as $key => $value) {
      if ($value) {
        $this->mapManager->addGeolocationToContentBundle($key);
        $current_available_filters = $this->configFactory()->get(MapManager::CONFIG_KEY)->get('available_filters') ?? [];
        $new_available_filters = $this->mapManager->buildFilters($key);
        $available_filters = array_unique(array_merge($current_available_filters, $new_available_filters));
        $this->configFactory()->getEditable(MapManager::CONFIG_KEY)
          ->set('available_filters', $available_filters)
          ->save();
      }
      else {
        $this->mapManager->removeGeolocationFromBundle($key);
        unset($enabled_content_types[$key]);
      }
    }

    $this->configFactory()->getEditable(MapManager::CONFIG_KEY)
      ->set('enabled_content_types', $enabled_content_types)
      ->save();

    $this->configFactory()->getEditable(MapManager::CONFIG_KEY)
      ->set('enabled_filters', $form_state->getValue('enabled_filters'))
      ->save();

    $this->configFactory()->getEditable(MapManager::CONFIG_KEY)
      ->set('display_map_helpers', $form_state->getValue('display_map_helpers'))
      ->save();

    $this->configFactory()->getEditable(MapManager::CONFIG_KEY)
      ->set('always_show_all_filters', $form_state->getValue('always_show_all_filters'))
      ->save();

    $this->configFactory()->getEditable(MapManager::CONFIG_KEY)
      ->set('max_visible_filters', $form_state->getValue('max_visible_filters'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  public function getListOfContentTypes(): array {
    $types = [];
    $contentTypes = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($contentTypes as $contentType) {
      $types[$contentType->id()] = $contentType->label();
    }
    return $types;
  }

}
