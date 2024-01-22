<?php

namespace Drupal\sis_relewise\OverviewFields;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\entity_overview\OverviewFieldInfoInterface;
use Drupal\entity_overview\OverviewFilter;

class ExternalField implements OverviewFieldInfoInterface {

  public function __construct() {
  }

  public function id(): string {
    return 'external';
  }

  public function label(): string|TranslatableMarkup {
    return t('External material');
  }

  public function isBase(): bool {
    return FALSE;
  }

  public function canBeExposed(): bool {
    return TRUE;
  }

  public function requiresFacets(): bool {
    return FALSE;
  }

  public function getWidgets(): array {
    return ['checkboxes' => t('Check boxes')];
  }

  public function updateFieldFormElementDefaultValue($value): mixed {
    return $value;
  }

  protected function getOptions() {
    $options = [
      'external' => t('External material')
    ];
    return $options;
  }

  public function getFieldFormElement(OverviewFilter $filter): array {
    return [
      '#type' => 'checkboxes',
      '#title' => $this->label(),
      '#options' => $this->getOptions(),
      '#default_value' => $filter->getFieldValue($this->id()) ?? []
    ];
  }

  public function getFieldFormTransform(OverviewFilter $filter): array {
    return [
      'type' => 'checkboxes',
      'title' => $this->label(),
      'options' => $this->getOptions(),
      'default_value' => $filter->getFieldValue($this->id()) ?? ''
    ];
  }

  public function setFieldFormElementAttribute(array &$form, $attribute, $value): void {
    $form['#' . $attribute] = $value;
  }

  public function getFilterValueFromFormStateValue($value): mixed {
    return $value;
  }

}
