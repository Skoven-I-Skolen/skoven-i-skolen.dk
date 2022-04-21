<?php

namespace Drupal\sis_overview\Repository;

use Drupal\Core\Database\Connection;

class TermRepository {

  private Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * @param $termIds
   * @param $limit
   * @param $page
   *
   * @return null
   */
  public function getNodeIdsByTaxonomyTermIds($termIds, $limit = 0, $page = 0) {
    $termIds = (array) $termIds;

    if (empty($termIds)) {
      return NULL;
    }

    $query = $this->database
      ->select('taxonomy_index', 'ti')
      ->fields('ti', ['nid'])
      ->condition('ti.tid', $termIds, 'IN');
    $query->leftJoin('node_field_data', 'n', 'ti.nid = n.nid');
    $query->orderBy('n.title', 'ASC')
      ->distinct(TRUE);

    if ($limit > 0) {
      $start = $limit * $page;
      $query->range($start, $limit);
    }

    return $query->execute()
      ->fetchCol();
  }

  /**
   * @param int $termId
   *
   * @return ?int
   */
  public function getNumberOfItemsWithTaxonomyTerm(int $termId): ?int {
    if (empty($termId)) {
      return NULL;
    }

    return $this->database
      ->select('taxonomy_index', 'ti')
      ->condition('ti.tid', $termId)
      ->countQuery()
      ->execute()
      ->fetchField();
  }

}
