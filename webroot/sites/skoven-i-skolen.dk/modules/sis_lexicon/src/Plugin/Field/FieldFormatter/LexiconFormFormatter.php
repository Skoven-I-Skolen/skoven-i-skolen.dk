<?php
namespace Drupal\sis_lexicon\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_overview\Plugin\Field\FieldFormatter\OverviewFormFormatter;
use Drupal\premium_articles\Plugin\Field\FieldFormatter\ArticleListFormatter;

/**
 * Plugin implementation of the 'article_filter_form' formatter.
 *
 * @FieldFormatter(
 *   id = "lexicon_overview_form",
 *   label = @Translation("Lexicon overview"),
 *   field_types = {
 *     "overview_filter"
 *   }
 * )
 */
class LexiconFormFormatter extends OverviewFormFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $options = $item->getValue();
      $options['entity_bundle'] = $items->getSetting('entity_bundle');
      $options['view_mode'] = $this->getSetting('view_mode');
      $elements[$delta] = \Drupal::formBuilder()->getForm('Drupal\sis_lexicon\Form\LexiconOverviewForm', $options);
    }

    return $elements;
  }

}
