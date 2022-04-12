<?php

namespace Drupal\sis_lexicon\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Plugin\Field\FieldWidget\OverviewFilterWidget;;

/**
 * Plugin implementation of the 'article_filter' widget for contacts.
 *
 * @FieldWidget(
 *   id = "lexicon_overview_widget",
 *   label = @Translation("Lexicon overview"),
 *   description = @Translation("Select options for filtering."),
 *   field_types = {
 *     "overview_filter"
 *   },
 *   multiple_values = TRUE
 * )
 */
class LexiconFilterWidget extends OverviewFilterWidget {

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['count'] = [
      '#type' => 'hidden',
      '#default_value' => 0,
    ];

    $element['sort'] = [
      '#type' => 'hidden',
      '#default_value' => 'alphabetical',
    ];

    if ($this->getFieldSetting('allow_facets')) {
      unset($element['facets']['#options']['count']);
      unset($element['facets']['#options']['sort']);

      $element['pagination'] = [
        '#type' => 'hidden',
        '#default_value' => FALSE,
      ];
    }

    return $element;
  }
}
