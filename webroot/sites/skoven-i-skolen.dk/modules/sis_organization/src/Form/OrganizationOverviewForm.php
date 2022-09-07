<?php

namespace Drupal\sis_organization\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewFilter;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrganizationOverviewForm extends OverviewFilterForm {

  static public function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_overview.manager'),
      $container->get('request_stack')
    );
  }

  public function buildForm(array $form, FormStateInterface $form_state, OverviewFilter $filter = NULL) {
    $form = parent::buildForm($form, $form_state, $filter);

    $form['search'] = [];

    $form['search']['title'] = [
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
      '#markup' => $this->t('Materials from @organization', [
        '@organization' => $this->request->get('profile')
          ->get('field_organization_address')->organization,
      ]),
    ];

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
    parent::buildEntitiesInContent($content, $entities, $filter);
    $profile = $this->request->get('profile');
    array_unshift($content['content'], [
      '#theme' => 'overview_list_item',
      '#headline' => t('See site-specific materials from @name', [
        '@name' => $profile->get('field_organization_address')->organization
      ]),
      '#attributes' => [
        'class' => ['inspiration-overview__item--maplink']
      ],
      '#link' => [
        'uri' => Url::fromUserInput('/'),
        'title' => 'Go to map'
      ]
    ]);
  }

}
