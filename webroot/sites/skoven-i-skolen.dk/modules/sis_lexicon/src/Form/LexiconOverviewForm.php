<?php

namespace Drupal\sis_lexicon\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewManager;
use Drupal\entity_overview\Services\OverviewFormStateService;
use Drupal\node\Entity\Node;
use Drupal\sis_lexicon\Services\LexiconContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LexiconOverviewForm extends OverviewFilterForm {

  const DEFAULT_LIMIT = 8;

  private ?LexiconContentDeliveryService $lexiconContentDelivery = NULL;

  function __construct(
    OverviewManager               $overviewManager,
    RequestStack                  $requestStack,
    LexiconContentDeliveryService $lexiconContentDelivery
  ) {
    parent::__construct($overviewManager, $requestStack);
    $this->lexiconContentDelivery = $lexiconContentDelivery;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_overview.manager'),
      $container->get('request_stack'),
      $container->get('sis_lexicon.content_delivery')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_overview_filter_lexicon_form';
  }


  public function buildForm(array $form, FormStateInterface $form_state, $options = []) {
    $form = parent::buildForm($form, $form_state, $options);

    $form['#attributes'] = [
      'onsubmit' => 'return false',
    ];

    /**
     * Add the filters
     */

    $form['content']['filters'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['lexicon__filter-wrapper']
      ]
    ];

    $form['content']['filters']['filter'] = $this->getContentDelivery()
      ->getFilters(self::DEFAULT_LIMIT, 0, ['query' => ['pager' => TRUE]]);

    /**
     * Add the search field and button
     */

    $form['content']['filters']['search'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['lexicon__search']
      ]
    ];

    $form['content']['filters']['search']['keyword'] = [
      '#type' => 'search',
      '#maxlength' => 64,
      '#size' => 64,
      '#theme_wrappers' => [],
      '#attributes' => [
        'class' => []
      ],
    ];

    $form['content']['filters']['search']['search'] = [
      '#type' => 'button',
      '#value' => $this->t('Search'),
      '#attributes' => [
        'data-twig-suggestion' => 'search_button',
      ],
      '#ajax' => [
        'callback' => '::performSearch',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];

    return $form;
  }

  public function performSearch($form, $form_state) {
    $keyword = $form_state->getValue('keyword');
    $response = new AjaxResponse();

    if (empty($keyword)) {
      return $response;
    }

    if (!$content = $this->getContentDelivery()
      ->getArticlesByKeyword($keyword)) {
      $content = [
        '#theme' => 'lexicon_search',
        '#articles' => 'No results found',
      ];
    }

    $response->addCommand(new ReplaceCommand('#lexicon-items', $content));

    return $response;
  }

  /**
   * @param array $content
   * @param Node[] $nodes
   * @param array $options
   */
  protected function buildEntitiesInContent(array &$content, array $entities, array $options) {
    $content['content']['articles'] = $this->getContentDelivery()
      ->getArticles('A', self::DEFAULT_LIMIT);
    $content['content']['articles']['#theme'] = 'lexicon';
    $content['content']['articles']['#pager'] = TRUE;
    $content['content']['#weight'] = 100;
  }

  private function getContentDelivery() {
    if ($this->lexiconContentDelivery instanceof LexiconContentDeliveryService) {
      return $this->lexiconContentDelivery;
    }

    return \Drupal::service('sis_lexicon.content_delivery');
  }

}
