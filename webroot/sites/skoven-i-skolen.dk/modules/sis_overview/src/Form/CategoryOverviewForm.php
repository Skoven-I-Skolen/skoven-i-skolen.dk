<?php

namespace Drupal\sis_overview\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryOverviewForm extends OverviewFilterForm {

  static public function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_overview.manager'),
      $container->get('request_stack')
    );
  }

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

  protected function buildEntitiesInContent(array &$content, array $entities, OverviewFilter $filter) {
    $content['content'] = [
      '#type' => 'markup',
      '#markup' => t('Ingen resultater fundet'),
    ];

    if ($entities) {
      parent::buildEntitiesInContent($content, $entities, $filter);
    }
  }

}
