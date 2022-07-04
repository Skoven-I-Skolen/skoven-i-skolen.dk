<?php

namespace Drupal\sis_season_wheel\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;

class SeasonWheelRepository {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private Connection $connection;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  private EntityTypeManager $entityTypeManager;

  public function __construct(Connection $connection, EntityTypeManager $entityTypeManager) {
    $this->connection = $connection;
    $this->entityTypeManager = $entityTypeManager;
  }

  public function getMonthsTaxonomyKeyedByMachineName() {
    return $this->connection->select('taxonomy_term_field_data', 't')
      ->fields('t', ['tid', 'machine_name'])
      ->condition('vid', 'month')
      ->execute()
      ->fetchAllKeyed(1,0);
  }
}
