<?php

namespace Drupal\sis_news\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_overview\Plugin\Field\FieldFormatter\OverviewFormFormatter;
use Drupal\layout_builder\Plugin\Block\InlineBlock;
use Drupal\sis_news\Form\NewsOverviewForm;
use Drupal\taxonomy\Entity\Term;

/**
 * Plugin implementation of the 'article_filter_form' formatter.
 *
 * @FieldFormatter(
 *   id = "news_overview_formatter",
 *   label = @Translation("News overview"),
 *   field_types = {
 *     "article_filter",
 *     "overview_filter"
 *   }
 * )
 */
class NewsOverviewFormatter extends OverviewFormFormatter {


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $parent = $items->getParent()->getEntity();

    $headline = '';
    if ($parent instanceof Term) {

      $headline = $parent->get('name')->view();
      if ($parent->hasField('field_overview_headline') && !$parent->get('field_overview_headline')->isEmpty()) {
        $headline = $parent->get('field_overview_headline')->view();
      }

    }

    if ($parent instanceof InlineBlock) {
      $headline = $parent->get('field_category_overview_headline')->view();
    }

    foreach ($items as $delta => $item) {
      $options = $item->getValue();
      $options['entity_bundle'] = $items->getSetting('entity_bundle');
      $options['view_mode'] = 'list';
      $options['headline'] = $headline;

      $elements[$delta] = \Drupal::formBuilder()->getForm(NewsOverviewForm::class, $options);
    }

    return $elements;
  }

}
