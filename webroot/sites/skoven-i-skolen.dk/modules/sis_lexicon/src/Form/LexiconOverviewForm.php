<?php

namespace Drupal\sis_lexicon\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewManager;
use Drupal\entity_overview\Services\OverviewFormStateService;
use Drupal\node\Entity\Node;
use Drupal\premium_articles\Form\ArticleFilterForm;
use Drupal\sis_lexicon\Services\LexiconContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LexiconOverviewForm extends OverviewFilterForm {

  /**
   * @var \Drupal\komponent_forms\Form\LexiconContentDeliveryService
   */
  private LexiconContentDeliveryService $lexiconContentDelivery;

  function __construct(
    OverviewManager               $overviewManager,
    RequestStack                  $requestStack,
    LexiconContentDeliveryService $lexiconContentDelivery
  ) {
    $this->overviewManager = $overviewManager;
    $this->request = $requestStack->getCurrentRequest();
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

  public function buildForm(array $form, FormStateInterface $form_state, $options = []) {
    $form = parent::buildForm($form, $form_state, $options);

    $form['#attributes'] = [
      'onsubmit' => 'return false',
    ];

    $form['content']['keyword'] = [
      '#type' => 'textfield',
      '#description' => $this->t('Enter search text and hit enter key.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => -100,
      // Attach AJAX callback.
      '#ajax' => [
        'callback' => '::performSearch',
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

    return $form;
  }

  public function performSearch($form, $form_state) {
    $keyword = $form_state->getValue('keyword');
    $response = new AjaxResponse();

    if (empty($keyword)) {
      return $response;
    }

    if(!$content = $this->lexiconContentDelivery->getArticlesByKeyword($keyword)) {
      return $response->addCommand(new HtmlCommand('#lexicon-items', 'No results found'));
    }

    $response->addCommand(new ReplaceCommand('#lexicon-items', $content));

    return $response;  }

  /**
   * @param array $content
   * @param Node[] $nodes
   * @param array $options
   */
  protected function buildEntitiesInContent(array &$content, array $entities, array $options) {
    $content['content']['filter'] = $this->lexiconContentDelivery->getFilters(1);
    $content['content']['articles'] = $this->lexiconContentDelivery->getArticles('A', 1);
    $content['content']['articles']['#load_more'] = TRUE;
  }

}
