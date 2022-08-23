<?php

namespace Drupal\premium_theme_helper\Repository;

use Drupal;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\node\NodeInterface;

class EntitySlideshowRepository {


  public function getEntitiesByBundleAndTerm(?string $bundle = NULL, array $field = NULL, int $limit = 4) {
    $query = $this->buildQuery($bundle, $field, $limit);
    return $query->execute();
  }

  /**
   * @param $limit
   * @param array $fields
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   */
  private function buildQuery(?string $entityBundle = NULL, array $fields, int $limit): QueryInterface {
    $query = Drupal::entityQuery('node')
      ->condition('status', NodeInterface::PUBLISHED);

    if($entityBundle) {
      $query->condition('type', $entityBundle);
    }

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

    $query->sort('created', 'DESC');
    return $query;
  }

}


