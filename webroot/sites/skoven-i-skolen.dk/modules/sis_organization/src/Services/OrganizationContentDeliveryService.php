<?php

namespace Drupal\sis_organization\Services;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Drupal\profile\Entity\Profile;
use Drupal\sis_organization\Repository\OrganizationRepository;

class OrganizationContentDeliveryService {

  /**
   * @var \Drupal\sis_organization\Repository\OrganizationRepository
   */
  private OrganizationRepository $organizationRepository;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  private EntityTypeManager $entityTypeManager;

  public function __construct(OrganizationRepository $organizationRepository, EntityTypeManager $entityTypeManager) {
    $this->organizationRepository = $organizationRepository;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   *
   * @param int $limit
   * @param int $page
   *
   * @return array
   */
  public function getOrganizations(int $limit, int $page) {
    $organizationIds = $this->organizationRepository->getOrganizations($limit, $page);
    $entities = Profile::loadMultiple($organizationIds);

    $profiles = $this->entityTypeManager
      ->getViewBuilder('profile')
      ->viewMultiple($entities, 'list');

    return [
      '#theme' => 'organization_overview',
      '#entities' => $profiles,
    ];
  }

  public function getRandomArticlesFromOrganization(): array {
    /** @var \Drupal\sis_organization\Repository\OrganizationRepository $repository */
    $repository = \Drupal::service('sis_organization.organization_repository');

    $articleIds = $repository->getRandomArticlesFromOrganizations(12);
    $entities = Node::loadMultiple($articleIds);

    return $this->entityTypeManager
      ->getViewBuilder('node')
      ->viewMultiple($entities, 'list');
  }

}
