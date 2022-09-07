<?php

namespace Drupal\sis_organization\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_overview\OverviewFilter;
use Drupal\entity_overview\Plugin\Field\FieldFormatter\OverviewFormFormatter;
use Drupal\sis_overview\Form\CategoryOverviewForm;

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
      $overview_id = $items->getSetting('overview');
      if (empty($overview_id)) {
        $entity_bundle = $items->getSetting('entity_bundle');
        $overview_id = str_replace('node.', '', $entity_bundle);
      }
      $filter = new OverviewFilter($overview_id, $item->getValue());
      $filter->setViewMode('list');

      $elements[$delta] = \Drupal::formBuilder()
        ->getForm('Drupal\sis_organization\Form\OrganizationOverviewForm', $filter);
    }

    return $elements;
  }

}
