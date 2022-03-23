<?php

namespace Drupal\sis_articles\Repository;

use Drupal;
use Drupal\node\NodeInterface;

class ArticleRepository {

  /**
   * Get articles by taxonomy
   *
   * @return int[]
   */
  public function fetchArticlesIdsByTaxonomy(array $fields, $limit = 10): ?array {
    $query = $this->buildQuery($fields, $limit);
    return $query->execute();
  }

  public function fetchRandomArticlesIdsByTaxonomy(array $fields, $limit = 10): ?array {
    $query = $this->buildQuery($fields, $limit);
    $query->addTag('sort_by_random');
    return $query->execute();
  }

  /**
   * @param $limit
   * @param array $fields
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   */
  private function buildQuery(array $fields, $limit): Drupal\Core\Entity\Query\QueryInterface {
    $query = Drupal::entityQuery('node')
      ->condition('type', 'article')
      ->condition('status', NodeInterface::PUBLISHED);

    if (!empty($limit)) {
      $query->range(0, $limit);
    }

    foreach ($fields as $field => $value) {

      if (empty($value)) {
        continue;
      }

      if (is_array($value)) {
        $query->condition($field, $value, 'IN');
        continue;
      }
      $query->condition($field, $value);

    }
    return $query;
  }
}
