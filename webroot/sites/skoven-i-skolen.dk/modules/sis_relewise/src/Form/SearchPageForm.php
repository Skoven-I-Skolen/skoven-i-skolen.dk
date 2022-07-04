<?php

namespace Drupal\sis_relewise\Form;

use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewManager;
use Drupal\node\Entity\Node;
use Drupal\relewise\DataTypes\Search\Sorting\Content\ContentAttributeSorting;
use Drupal\relewise\DataTypes\Search\Sorting\Content\ContentDataSorting;
use Drupal\relewise\DataTypes\Search\Sorting\Content\ContentPopularitySorting;
use Drupal\relewise\Relewise;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SearchPageForm extends OverviewFilterForm {

  protected $count = 0;

  /**
   * @var \Drupal\relewise\Relewise
   */
  protected $relewise;

  function __construct(Relewise $relewise, OverviewManager $overviewManager, RequestStack $requestStack) {
    $this->relewise = $relewise;
    parent::__construct($overviewManager, $requestStack);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('relewise'),
      $container->get('entity_overview.manager'),
      $container->get('request_stack')
    );
  }

  public function getFormId() {
    return 'sis_search_page';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $options = []) {
    $options = [
      'entity_bundle' => 'node.article',
      'facets' => [
        'text',
        'sort',
        'field_article_type',
        'field_subject',
        'field_class',
        'field_season',
        'field_location',
      ],
      'fields' => [
        'field_article_type' => [],
        'field_subject' => [],
        'field_class' => [],
        'field_season' => [],
        'field_location' => [],
        'term' => ''
      ],
      'pagination' => TRUE,
//      'show_total' => 'filtered',
      'count' => 15,
      'sort' => 'relevant',
      'view_mode' => 'list'
    ];
    $form = parent::buildForm($form, $form_state, $options);
    $form['#cache']['max-age'] = 0;

    $title = $this->t('Search');
    if (!empty($form_state->get(['fields', 'text']))) {
      $title = $this->t('Search results for “@keyword”', ['@keyword' => $form_state->get(['fields', 'text'])]);
    }
    $form['facets']['text']['#type'] = 'search';
    $form['facets']['text']['#title'] = $title;

    $form['most_popular'] = [
      '#theme' => 'sis_relewise_most_popular'
    ];

    $form['keyword'] = [
      '#markup' => $form_state->get(['fields', 'term'])
    ];

    $form['number_of_results'] = [
      '#markup' => $this->t('@count result found', ['@count' => $this->count])
    ];

    // Remove the throbber, since its messing with our styles
    foreach($form['facets'] as $key => $facets) {
      if (isset($facets['#ajax'])) {
        $form['facets'][$key]['#ajax']['progress']['type'] = 'none';
      }
    }

    return $form;
  }

  /**
   * Function for retrieving the entities to be displayed. Overwrite for when a custom query is necessary.
   *
   * @param string $entity_bundle
   * @param array $options
   * @param int $page
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   */
  protected function getEntitiesForBuilding($entity_bundle, array $options, $page = 0) {
    $entities = [];
    $this->count = 0;
    $fields = [];
    foreach ($options['fields'] as $key => $value) {
      if ($key !== 'term' && !empty($value)) {
        $fields[$key] = $value;
      }
    }
    $sorting = NULL;
    switch ($options['sort']) {
      case 'newest':
        $sorting = new ContentDataSorting('changed');
        break;
      case 'oldest':
        $sorting = new ContentDataSorting('changed', 'Ascending');
        break;
      case 'alphabetical':
        $sorting = new ContentAttributeSorting('DisplayName', 'Ascending');
        break;
      case 'popular':
        $sorting = new ContentPopularitySorting();
        break;
    }
    $result = $this->relewise->searchContent($options['fields']['term'], $fields, $sorting, $options['count'], $page);
    if ($result !== FALSE && !empty($result['hits']) && $result['hits'] > 0) {
      $this->count = $result['hits'];
      $content = [];
      foreach ($result['results'] as $item) {
        $content_info = explode(':', $item['contentId']);
        $content[$content_info[0]] = empty($content[$content_info[0]]) ? [] : $content[$content_info[0]];
        $content[$content_info[0]][] = $content_info[1];
      }
      foreach ($content as $entity_type => $entity_ids) {
        $entities += \Drupal::entityTypeManager()->getStorage($entity_type)->loadMultiple($entity_ids);
      }
    }
    if ($options['pagination']) {
      $this->request->query->set('page', $page);
      /** @var \Drupal\Core\Pager\PagerManagerInterface $pagerManager */
      $pagerManager = \Drupal::service('pager.manager');
      $element = $pagerManager->getMaxPagerElementId() + 1;
      $pagerManager->createPager($this->count, $options['count'], $element);
    }
    return $entities;
  }

  /**
   * Function for getting total number of entities. Overwrite for when a custom query is necessary.
   *
   * @param string $entity_bundle
   * @param array $options
   * @param int $shown
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  protected function getEntitiesTotal($entity_bundle, array $options, $shown) {
    $total = \Drupal::entityQuery('node')
      ->condition('type', ['article', 'course', 'contact', 'page'], 'IN')
      ->condition('status', 1)
      ->count()->execute();
    $count = $this->count;
    return t('Showing @count out of @total', ['@count' => $count, '@total' => $total]);
  }

  /**
   * Function for building the display of the entities. Overwrite for building overviews with custom layouts and views.
   *
   * @param array $content
   * @param Node[] $nodes
   * @param array $options
   */
  protected function buildEntitiesInContent(array &$content, array $nodes, array $options) {
    if (empty($nodes)) {
      $content['content'] = [
        '#markup' => $this->t('Your search yielded no results.')->__toString()
      ];
    } else {
      $content['content'] = \Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->viewMultiple($nodes, $options['view_mode']);
    }
  }

  /**
   * AJAX callback for refreshing content.
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return mixed
   */
  public function contentCallback($form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Ajax\AjaxResponse $response */
    $response = parent::contentCallback($form, $form_state);
    if (empty($form_state->getValue('term'))) {
      $title = $this->t('Search results');
    } else {
      $title = $this->t('Search results for “@keyword”', ['@keyword' => $form_state->getValue('term')])
        ->__toString();
    }
    $response->addCommand(new HtmlCommand('.article-list__filters .js-form-item-term label', $title));
    return $response;
  }
  
}
