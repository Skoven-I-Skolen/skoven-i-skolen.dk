<?php

namespace Drupal\sis_overview\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_overview\Plugin\Field\FieldFormatter\OverviewFormFormatter;

/**
 * Plugin implementation of the 'article_filter_form' formatter.
 *
 * @FieldFormatter(
 *   id = "taxonomy_overview_formatter",
 *   label = @Translation("Taxonomy overview"),
 *   field_types = {
 *     "article_filter",
 *     "overview_filter"
 *   }
 * )
 */
class TaxonomyOverviewFormatter extends OverviewFormFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $options = $item->getValue();
      $options['entity_bundle'] = $items->getSetting('entity_bundle');
      $options['view_mode'] = 'list'; //$this->getSetting('view_mode');

      $options['facets'] = [
        'field_class',
        'field_location',
        'field_season',
        'field_subject',
      ];

      $options['count'] = 16;
      $options['pagination'] = true;

      $elements[$delta] = \Drupal::formBuilder()
        ->getForm('Drupal\sis_overview\Form\TaxonomyOverviewForm', $options);
    }

    return $elements;
  }

}
