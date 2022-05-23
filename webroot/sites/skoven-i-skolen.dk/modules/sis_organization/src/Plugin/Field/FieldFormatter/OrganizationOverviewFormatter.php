<?php

namespace Drupal\sis_organization\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_overview\Plugin\Field\FieldFormatter\OverviewFormFormatter;

/**
 * Plugin implementation of the 'overview_filter_form' formatter.
 *
 * @FieldFormatter(
 *   id = "organization_overview_formatter",
 *   label = @Translation("Organization overview"),
 *   field_types = {
 *     "article_filter",
 *     "overview_filter"
 *   }
 * )
 */
class OrganizationOverviewFormatter extends OverviewFormFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $options = $item->getValue();
      $options['entity_bundle'] = $items->getSetting('entity_bundle');
      $options['view_mode'] = 'list'; //$this->getSetting('view_mode');
      $elements[$delta] = \Drupal::formBuilder()
        ->getForm('Drupal\sis_organization\Form\OrganizationOverviewForm', $options);
    }

    return $elements;
  }

}
