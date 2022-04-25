<?php

namespace Drupal\sis_overview\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_overview\Plugin\Field\FieldFormatter\OverviewFormFormatter;
use Drupal\sis_overview\Form\CategoryOverviewForm;

/**
 * Plugin implementation of the 'article_filter_form' formatter.
 *
 * @FieldFormatter(
 *   id = "category_overview_formatter",
 *   label = @Translation("Category overview"),
 *   field_types = {
 *     "article_filter",
 *     "overview_filter"
 *   }
 * )
 */
class CategoryOverviewFormatter extends OverviewFormFormatter {


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $block = $items->getParent()->getEntity();
    $headline = $block->get('field_category_overview_headline')->view();

    foreach ($items as $delta => $item) {
      $options = $item->getValue();
      $options['entity_bundle'] = $items->getSetting('entity_bundle');
      $options['view_mode'] = 'list';
      $options['headline'] = $headline;

      $elements[$delta] = \Drupal::formBuilder()->getForm(CategoryOverviewForm::class, $options);
    }

    return $elements;
  }

}
