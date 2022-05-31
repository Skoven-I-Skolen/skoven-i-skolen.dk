<?php

namespace Drupal\sis_lexicon\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\NodeInterface;

class LexiconRepository {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private Connection $database;

  private EntityTypeManager $entityTypeManager;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public function getEntityIdsByInitialLetter(string $initialLetter, $limit = 2, $page = 0): array {

    $query = $this->database->select('node_field_data', 'n')->fields('n', ['nid']);

    $query->leftJoin('node__field_article_type', 'a', 'n.nid = a.entity_id');
    $query->leftJoin('taxonomy_term_field_data', 't', 'a.field_article_type_target_id = t.tid');

    $query->condition('n.type', 'article')
      ->condition('n.status', NodeInterface::PUBLISHED)
      ->condition('n.title', strtolower($initialLetter) . '%', 'LIKE')
      ->condition('t.machine_name','lexicon');

    if ($limit >= 1) {
     $query->range($page * $limit, $limit);
    }

    return $query->execute()
      ->fetchAllKeyed(0,0);
  }

  public function getEntityIdsByKeyword(string $keyword, $limit = 0, $page = 0): array {

    $query = $this->database->select('node_field_data', 'n')->fields('n', ['nid']);

    $query->leftJoin('node__field_article_type', 'a', 'n.nid = a.entity_id');
    $query->leftJoin('taxonomy_term_field_data', 't', 'a.field_article_type_target_id = t.tid');

    $query->condition('n.type', 'article')
      ->condition('n.status', NodeInterface::PUBLISHED)
      ->condition('n.title', '%'. $keyword . '%', 'LIKE')
      ->condition('t.machine_name','lexicon');

    if ($limit >= 1) {
      $query->range($page * $limit, $limit);
    }

    return $query->execute()
      ->fetchAllKeyed(0,0);
  }

  public function getTotalNumberOfArticlesByLetter(string $letter) {

    $query = $this->database->select('node_field_data', 'n')->fields('n', ['nid']);

    $query->leftJoin('node__field_article_type', 'a', 'n.nid = a.entity_id');
    $query->leftJoin('taxonomy_term_field_data', 't', 'a.field_article_type_target_id = t.tid');

    $query->condition('n.type', 'article')
      ->condition('n.status', NodeInterface::PUBLISHED)
      ->condition('n.title', strtolower($letter) . '%', 'LIKE')
      ->condition('t.machine_name','lexicon');
    
    return $query->execute()
      ->fetchAllKeyed(0,0);
  }
}
