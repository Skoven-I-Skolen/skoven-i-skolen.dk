<?php

namespace Drupal\sis_relewise\OverviewFields;

use Drupal\entity_overview\OverviewFields\OverviewFieldBase;
use Drupal\entity_overview\OverviewFilter;

class ExternalField extends OverviewFieldBase {

  public function __construct() {
    parent::__construct('external', t('External material'));
  }

  public function getWidgets(): array {
    return ['checkboxes' => t('Check boxes')];
  }

  protected function getOptions() {
    $options = [
      'external' => t('External material')
    ];
    return $options;
  }

  /**
   * @inheritDoc
   */
  public function getFieldFormElement(OverviewFilter $filter): array {
    return parent::getFieldFormElement($filter) + ['#options' => $this->getOptions()];
  }

  /**
   * @inheritDoc
   */
  public function getFieldFormTransform(OverviewFilter $filter): array {
    $transformation = parent::getFieldFormTransform($filter) + ['options' => []];
    foreach ($this->getOptions() as $key => $value) {
      $transformation['options'][] = [
        'key' => $key,
        'value' => $value
      ];
    }
    return $transformation;
  }

  public function getFilterValueFromFormStateValue($value): mixed {
    if ($value['external']) {
      return ['external'];
    }

    return NULL;
  }

}
