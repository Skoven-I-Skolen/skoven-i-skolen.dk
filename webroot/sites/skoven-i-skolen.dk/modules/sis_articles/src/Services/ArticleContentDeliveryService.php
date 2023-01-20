<?php

namespace Drupal\sis_articles\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\sis_articles\Repository\ArticleRepository;
use Drupal\taxonomy\Entity\Term;

class ArticleContentDeliveryService {

  private ArticleRepository $articleRepository;

  private SeasonService $seasonService;

  private $fieldNameToVidMapping = [
    'field_class' => 'class',
    'field_subject' => 'subjects',
    'field_location' => 'location',
    'field_season' => 'season',
    'field_time' => 'time',
    'field_article_months' => 'month',
  ];

  /**
   * @var \Drupal\Core\Entity\EntityViewBuilder
   */
  private EntityTypeManagerInterface $entityTypeManager;

  public function __construct(ArticleRepository $articleRepository, SeasonService $seasonService, EntityTypeManagerInterface $entityTypeManager) {
    $this->articleRepository = $articleRepository;
    $this->seasonService = $seasonService;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Get articles entities based on the current season
   *
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]
   */
  public function getArticlesForCurrentSeason(): ?array {
    $season = $this->seasonService->getCurrentSeasonTermId();
    $articleIds = $this->articleRepository->fetchArticlesIdsByTaxonomy([
      'field_season' => $season,
    ]);

    if (!empty($articleIds)) {
      return Node::loadMultiple($articleIds);
    }

    return NULL;
  }

  public function getInspirationalArticlesForCurrent(NodeInterface $node) {

    $fields = [
      'field_class',
      'field_subject',
      'field_related_terms'
    ];

    $inspirational = [];

    foreach ($fields as $field) {
      if ($node->hasField($field) && !$node->get($field)->isEmpty()) {
        if ($node->getType() === 'news') {
          $entities = $node->get($field)->getValue();
          foreach ($entities as $entity) {
            $entity = Term::load($entity['target_id']);
            $title = $entity->label();
            $link = (string) $entity->toLink()->toString();
            $inspirational[$link] = $this->getRandomArticlesByVid(
              $entity->get('vid')->getString(), $entity->get('tid')
              ->getString());
          }
        }
        else {
          /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
          $entity = $node->get($field)->entity;
          if ($entity) {
            $title = $entity->label();
            $link = (string) $entity->toLink()->toString();
            $inspirational[$link] = $this->getRandomArticlesByFields([$field], $node);
          }
        }
      }
    }
    return $inspirational;
  }

  public function getRandomInspirationalArticles() {
    $inspiration = [];

    $fields = [
      'field_class',
      'field_subject',
    ];

    foreach ($fields as $field) {
      $terms = $this->entityTypeManager
        ->getStorage('taxonomy_term')
        ->loadTree($this->fieldNameToVidMapping[$field]);
      $random_term = $terms[array_rand($terms)];
      $nodes = $this->entityTypeManager
        ->getStorage('node')
        ->loadByProperties([
          $field => $random_term->tid,
          'field_search_exclude' => 0
        ]);

      if ($nodes) {
        if (count($nodes) > 1) {
          $random_keys = array_rand($nodes, 2);
          $inspiration[$random_term->name][] = $nodes[$random_keys[0]];
          $inspiration[$random_term->name][] = $nodes[$random_keys[1]];
        }
        else {
          $random_keys = array_rand($nodes);
          $inspiration[$random_term->name][] = $nodes[$random_keys];
        }
      }
    }
    return $inspiration;
  }

  /**
   * Get related articles based on taxonomy
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node object to get related articles for.
   *
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]
   */
  function getRelatedArticles(NodeInterface $node): ?array {
    // List of fields to filter by.
    $taxonomyFields = ['field_subject', 'field_class', 'field_location'];
    return $this->getRelatedArticlesByFields($taxonomyFields, $node);

  }

  /**
   * Get array of field data.
   *
   * @param array $fieldNames
   *   Array containing the name of the field to get data for.
   *
   * @param $node
   *   The node object to get data on.
   *
   * @return array
   *   Array containing the field values
   */
  private function getFieldValues(array $fieldNames, $node): array {
    $fields = [];
    foreach ($fieldNames as $key => $fieldName) {
      $fields[$fieldName] = $this->getFieldValue($fieldName, $node);
    }

    return $fields;
  }

  /**
   * Perform validation and return data for field.
   *
   * @param string $fieldName
   *   The name if the field to get.
   * @param \Drupal\node\NodeInterface $node
   *   The node object to get data from.
   *
   * @return mixed|null
   */
  private function getFieldValue(string $fieldName, NodeInterface $node) {
    if ($node->hasField($fieldName) && !$node->get($fieldName)->isEmpty()) {
      return $node->get($fieldName)->first()->getValue();
    }

    return NULL;
  }

  /**
   * @return \Drupal\sis_articles\Repository\ArticleRepository
   */
  public function getArticleRepository(): ArticleRepository {
    return $this->articleRepository;
  }

  /**
   * Get a list of rendered articles created by a specific user.
   *
   * @param int $user_id
   *   Array containing the name of the field to get data for.
   */
  public function getArticlesByUser($user_id): array {
    // Fetch the article ids created by the user ID.
    if ($articleIds = $this->articleRepository->fetchArticleIdsByUser($user_id)) {
      $articles = Node::loadMultiple($articleIds);
      return $this->entityTypeManager->getViewBuilder('node')->viewMultiple($articles, 'list');
    }
    return [];
  }

  public function getUnpublishedContentByUser($user_id) {
    $nodes = \Drupal::entityTypeManager()->getStorage('node')
      ->loadByProperties(['status' => 0, 'uid' => $user_id]);
    return $this->entityTypeManager->getViewBuilder('node')->viewMultiple($nodes, 'list');
  }

  /**
   * @return \Drupal\sis_articles\Services\SeasonService
   */
  public function getSeasonService(): SeasonService {
    return $this->seasonService;
  }

  /**
   * Get articles by field values
   *
   * @param array $fieldNames
   *   Array containing the name of the fields to filter on.
   *
   * @param \Drupal\node\NodeInterface $node
   *  The node object to get the field values from
   * @param int $limit
   *  Number of items to fetch
   *
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]|null
   */
  public function getRelatedArticlesByFields(array $fieldNames, NodeInterface $node, int $limit = 10): ?array {
    // Load the values of the fields and assign them to an array
    $fields = $this->getFieldValues($fieldNames, $node);
    $fields['field_season'] = $this->seasonService->getCurrentSeasonTermId();

    // Fetch the related article ids based the current filter fields.
    if ($articleIds = $this->articleRepository->fetchArticlesIdsByTaxonomy($fields, $limit)) {
      return $this->loadArticles($node, $articleIds);
    }

    return null;
  }

  /**
   * Get articles by article type.
   *
   * @param int $termId
   *   The taxonomy term is representing the article type.
   * @param int $limit
   *   The number of items to return (default is 4)
   * @param string $viewMode
   *   The view mode to use when rendering the articles (default is "list")
   *
   * @return array
   *   A array of renderable articles.
   */
  public function getArticlesByType($termId, int $limit = 4, string $viewMode = 'list'): array {

    $fields = [
      'field_article_type' => $termId,
    ];

    // Fetch the related article ids based the current filter fields.
    if ($articleIds = $this->articleRepository->fetchArticlesIdsByTaxonomy($fields, $limit)) {
      $articles = Node::loadMultiple($articleIds);

      return $this->entityTypeManager
        ->getViewBuilder('node')
        ->viewMultiple($articles,  $viewMode);
    }

    return [];
  }

  /**
   * Fetch random articles by field values
   *
   * @param array $fieldNames
   *   Array containing the name of the fields to filter on.
   *
   * @param \Drupal\node\NodeInterface $node
   *  The node object to get the field values from
   * @param int $limit
   *  Number of items to fetch
   *
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]|null
   */
  public function getRandomArticlesByFields(array $fieldNames, NodeInterface $node, int $limit = 2): ?array {
    // Load the values of the fields and assign deom to an array
    $fields = $this->getFieldValues($fieldNames, $node);
    $fields['field_season'] = $this->seasonService->getCurrentSeasonTermId();

    // Fetch the related article ids based the current filter fields.
    if ($articleIds = $this->articleRepository->fetchRandomArticlesIdsByTaxonomy($fields, $limit)) {
      return $this->loadArticles($node, $articleIds);
    }

    return null;
  }

  public function getRandomArticlesByVid(string $vid, string $term_id) {
    if ($vid === 'subjects') {
      $vid = 'subject';
    }
    $result = $this->entityTypeManager->getStorage('node')
      ->loadByProperties(['field_' . $vid => $term_id]);
    $nodes = [];
    if ($result) {
      $rand_keys = array_rand($result, 3);
      foreach ($rand_keys as $nid) {
        $nodes[] = Node::load($nid);
      }
    }
    return $nodes;
  }

  /**
   * @param \Drupal\node\NodeInterface|null $currentNode
   * @param array|null $articleIds
   *
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]|null
   */
  public function loadArticles(?NodeInterface $currentNode, ?array $articleIds): ?array {
    if (empty($articleIds)) {
      return NULL;
    }

    if (empty($currentNode)) {
      return Node::loadMultiple($articleIds);
    }

    // We du not want to show the current node as related So we remove it
    if (($key = array_search($currentNode->id(), $articleIds)) !== FALSE) {
      unset($articleIds[$key]);
    }

    return Node::loadMultiple($articleIds);
  }

}
