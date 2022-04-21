<?php

namespace Drupal\sis_overview\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;

class TaxonomyOverviewForm extends OverviewFilterForm {

  public function buildForm(array $form, FormStateInterface $form_state, array $options = []) {
    $form = parent::buildForm($form, $form_state, $options);

    $form['search'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['overview__search']
      ],
      '#weight' => -100
    ];

    $form['search']['title'] = [
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
      '#markup' => $this->t('All @type pages', ['@type' => strtolower($this->request->get('taxonomy_term')->label())])
    ];

    $form['search']['keyword'] = [
      '#type' => 'textfield',
      '#maxlength' => 64,
      '#size' => 64,
      // Attach AJAX callback.
      '#ajax' => [
        'callback' => '::contentCallback',
        // Set focus to the textfield after hitting enter.
        'disable-refocus' => FALSE,
        // Trigger when user hits enter key.
        'event' => 'change',
        // Trigger after each key press.
        // 'event' => 'keyup'
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Searching ...'),
        ],
      ],
    ];

    $form['search']['search'] = [
      '#type' => 'button',
      '#value' => $this->t('Search'),
    ];

    return $form;
  }

}
