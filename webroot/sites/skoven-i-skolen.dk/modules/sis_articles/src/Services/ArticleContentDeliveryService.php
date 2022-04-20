<?php

namespace Drupal\sis_articles\Services;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\sis_articles\Repository\ArticleRepository;

class ArticleContentDeliveryService {

  private ArticleRepository $articleRepository;

  private SeasonService $seasonService;

  public function __construct(ArticleRepository $articleRepository, SeasonService $seasonService) {
    $this->articleRepository = $articleRepository;
    $this->seasonService = $seasonService;
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
      'field_subject'
    ];

    $inspirational = [];
    foreach ($fields as $field) {
      if ($node->hasField($field) && !$node->get($field)->isEmpty()) {
        $title = $node->get($field)->entity->label();
        $inspirational[$title] = $this->getRandomArticlesByFields([$field], $node);
      }
    }
    return $inspirational;
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
    // Load the values of the fields and assign deom to an array
    $fields = $this->getFieldValues($fieldNames, $node);
    $fields['field_season'] = $this->seasonService->getCurrentSeasonTermId();

    // Fetch the related article ids based the current filter fields.
    if ($articleIds = $this->articleRepository->fetchArticlesIdsByTaxonomy($fields, $limit)) {
      return $this->loadArticles($node, $articleIds);
    }

    return null;
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

  /**
   * @param \Drupal\node\NodeInterface $currentNode
   * @param array|null $articleIds
   *
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]|null
   */
  public function loadArticles(NodeInterface $currentNode, ?array $articleIds): ?array {
    if (empty($articleIds)) {
      return NULL;
    }

    // We du not want to show the current node as related So we remove it
    if (($key = array_search($currentNode->id(), $articleIds)) !== FALSE) {
      unset($articleIds[$key]);
    }

    return Node::loadMultiple($articleIds);

  }

}
