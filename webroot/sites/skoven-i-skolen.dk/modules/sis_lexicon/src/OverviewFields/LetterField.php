<?php

namespace Drupal\sis_lexicon\OverviewFields;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\entity_overview\OverviewFieldInfoInterface;
use Drupal\entity_overview\OverviewFilter;

class LetterField implements OverviewFieldInfoInterface {

  public function __construct() {
  }

  public function id(): string {
    return 'letter';
  }

  public function label(): string|TranslatableMarkup {
    return t('Initial letter');
  }

  public function getWidgets(): array {
    return ['radios' => t('Radios')];
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

  public function updateFieldFormElementDefaultValue($value): mixed {
    return $value;
  }

  protected function getOptions() {
    $alphabet = array_merge(range('A', 'Z'), ['Æ', 'Ø', 'Å']);
    $options = [
      '' => 'Alle',
    ];
    foreach ($alphabet as $letter) {
      $options[$letter] = $letter;
    }
    return $options;
  }

  public function getFieldFormElement(OverviewFilter $filter): array {
    return [
      '#type' => 'radios',
      '#attributes' => ['class' => ['lexicon__letters']],
      '#title' => $this->label(),
      '#options' => $this->getOptions(),
      '#default_value' => $filter->getFieldValue($this->id()) ?? ''
    ];
  }

  public function getFieldFormTransform(OverviewFilter $filter): array {
    return [
      'type' => 'radios',
      'title' => $this->label(),
      'options' => $this->getOptions(),
      'default_value' => $filter->getFieldValue($this->id()) ?? ''
    ];
  }

}
