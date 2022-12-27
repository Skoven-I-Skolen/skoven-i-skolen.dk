<?php

namespace Drupal\sis_organization\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\NodeInterface;

class OrganizationRepository {

  private EntityTypeManager $entityTypeManager;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private Connection $database;

  public function __construct(EntityTypeManager $entityTypeManager, Connection $database) {
    $this->entityTypeManager = $entityTypeManager;
    $this->database = $database;
  }


  public function getOrganizations(int $limit = 10, int $page = 0) {
    $query = $this->entityTypeManager->getStorage('profile')->getQuery();
    $query->accessCheck(TRUE);
    $query->condition('status', NodeInterface::PUBLISHED);
    $query->condition('uid.entity:user.roles', 'organization');
    $query->sort('field_organization_address.organization');

    if ($page > 0) {
        $query->pager($limit, $page);
    }

    return $query->execute();
  }


  public function getNumberOfOrganizations() {
    return $this->entityTypeManager->getStorage('profile')
      ->getQuery()
      ->accessCheck(TRUE)
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('uid.entity:user.roles', 'organization')
      ->count()
      ->execute();
  }

  /**
   * Get articles from organizations
   *
   * @return int[]
   */
  public function getRandomArticlesFromOrganizations($limit = 0): array {
    $query = $this->database->select('node_field_data','n')
      ->fields('n', ['nid']);
    $query->leftJoin('user__roles', 'u', 'u.entity_id = n.uid');

    $query->condition('n.status', NodeInterface::PUBLISHED)
      ->condition('u.roles_target_id', 'organization');

    if ($limit > 0) {
      $query->range(0, $limit);
    }

    return $query->execute()->fetchCol();
  }

}
