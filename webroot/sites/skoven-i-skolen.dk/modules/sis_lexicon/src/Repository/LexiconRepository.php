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

  public function getEntityIdsByInitialLetter(string $initialLetter, $limit = 2): array {
    $query = $this->database->select('node_field_data', 'n')
      ->fields('n', ['nid'])
      ->condition('type', 'article')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('title', strtolower($initialLetter) . '%', 'LIKE')
      ->range(0, $limit);

    return $query->execute()
      ->fetchAllKeyed(0,0);

  }

}
