<?php

namespace Drupal\sis_overview\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewFilter;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Plugin\views\wizard\TaxonomyTerm;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryOverviewForm extends OverviewFilterForm {

  public function buildForm(array $form, FormStateInterface $form_state, OverviewFilter $filter = NULL, $headline = '') {
    $form = parent::buildForm($form, $form_state, $filter);

    $form['#attributes'] = [
      'onsubmit' => 'return false',
    ];

    $form['search'] = [];

    if ($headline) {
      $form['search']['title'] = $headline;
    }

    if (isset($form['facets']['text'])) {
      $form['facets']['text'] += [
        '#type' => 'search',
        '#title' => '',
        '#maxlength' => 64,
        '#size' => 64,
        '#theme_wrappers' => [],
        '#attributes' => [
          'class' => []
        ],
      ];
      if (!empty($filter->getFieldValue('field_article_type'))) {
        $tid = $filter->getFieldValue('field_article_type');
        $term = Term::load($tid[0]);
        $form['facets']['text']['#attributes']['placeholder'] = $this->t('Search in') . ' ' . strtolower($term->label());
      }
    }

    $form['search']['search'] = [
      '#type' => 'button',
      '#value' => $this->t('Search'),
      '#attributes' => [
        'data-twig-suggestion' => 'search_button',
      ],
      '#ajax' => [
        'callback' => '::contentCallback',
        'event' => 'click',
        'wrapper' => 'overview-form-contents',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];

    $form['content']['pager']['#quantity'] = 5;

    return $form;
  }

}
