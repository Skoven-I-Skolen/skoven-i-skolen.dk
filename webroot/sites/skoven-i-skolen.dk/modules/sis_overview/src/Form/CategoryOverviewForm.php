<?php

namespace Drupal\sis_overview\Form;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewManager;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryOverviewForm extends OverviewFilterForm {

  static public function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_overview.manager'),
      $container->get('request_stack')
    );
  }

  public function buildForm(array $form, FormStateInterface $form_state, array $options = []) {
    $form = parent::buildForm($form, $form_state, $options);

    $form['#attributes'] = [
      'onsubmit' => 'return false',
    ];

    $form['search'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['overview__search'],
        'placeholder' => $this->t('Search within business card')
      ],
      '#weight' => -100,
    ];

    if ($options['headline']) {
      $form['search']['title'] = $options['headline'];
    }

    $form['search']['keyword'] = [
      '#type' => 'search',
      '#maxlength' => 64,
      '#size' => 64,
      '#theme_wrappers' => [],
      '#attributes' => [
        'class' => []
      ],
    ];

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

    return $form;
  }

  protected function buildEntitiesInContent(array &$content, array $entities, array $options) {
    $content['content'] = ['#markup' => atom_str('overview-no-results')];

    if ($entities) {
      parent::buildEntitiesInContent($content, $entities, $options);
    }
  }


  protected function getEntitiesForBuilding($entity_bundle, array $options, $page = 0) {
    return $this->getEntities($entity_bundle, $options, $page);
  }

  private function getEntities($entity_bundle, array $options, $page = 0) {
    $profile = $this->request->get('profile');
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'article')
      ->condition('status', NodeInterface::PUBLISHED);

    if ($keyword = $options['keyword']) {
      $query->condition('title', '%' . $keyword . '%', 'LIKE');
    }

    // Loop through the fields to filter the result
    foreach ($options['fields'] as $field => $value) {
      if (empty($value)) {
        continue;
      }

      $operator = (is_array($value)) ? 'IN' : NULL;
      $query->condition($field, $value, $operator);
    }

    // Set pagination
    if ($options['pagination']) {
      $this->request->query->set('page', $page);
      $query->pager($options['count']);
    }

    $query->sort('title');

    $result = $query->execute();
    return Node::loadMultiple($result);
  }

  /**
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  protected function optionsFromFormState(FormStateInterface $form_state) {
    $entity_bundle = $form_state->get('entity_bundle');
    $options = [
      'entity_bundle' => $form_state->get('entity_bundle'),
      'fields' => $form_state->get('fields'),
      'count' => $form_state->getValue('count') ?? $form_state->get('count'),
      'view_mode' => $form_state->get('view_mode'),
      'pagination' => $form_state->get('pagination'),
      'sort' => $form_state->getValue('sort') ?? $form_state->get('sort'),
      'show_total' => $form_state->get('show_total') ?? $this->overviewManager->getShowTotal($entity_bundle),
      'page' => $form_state->getValue('page') ?? $form_state->get('page') ?? 0,
      'keyword' => $form_state->getValue('keyword') ?? $form_state->get('keyword') ?? NULL,
    ];

    $this->request->query->set('page', $options['page']);
    foreach ($options['fields'] as $key => $value) {
      if ($form_state->hasValue($key)) {
        if (is_array($form_state->getValue($key))) {
          $result = [];
          foreach ($form_state->getValue($key) as $value2) {
            if ($value2) {
              $result[] = $value2;
            }
          }
          $options['fields'][$key] = $result;
        }
        else {
          $options['fields'][$key] = $form_state->getValue($key);
        }
      }
    }

    return $options;
  }

}
