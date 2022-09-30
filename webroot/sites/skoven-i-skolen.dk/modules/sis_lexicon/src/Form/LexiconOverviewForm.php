<?php

namespace Drupal\sis_lexicon\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewFilter;
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


  public function buildForm(array $form, FormStateInterface $form_state, OverviewFilter $filter = NULL) {
    if (!is_null($filter)) {
      $facets = $filter->getFacets();
      $facets[] = 'letter';
      $filter->setFacets($facets);
    }
    $form = parent::buildForm($form, $form_state, $filter);

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

    return $form;
  }

  /**
   * @inheritDoc
   */
  protected function buildEntitiesInContent(array &$content, array $entities, OverviewFilter $filter) {
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

    return $this->lexiconContentDelivery = \Drupal::service('sis_lexicon.content_delivery');
  }

}
