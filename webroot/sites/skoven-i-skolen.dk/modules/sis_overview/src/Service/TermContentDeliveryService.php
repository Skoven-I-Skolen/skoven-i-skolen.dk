<?php

namespace Drupal\sis_overview\Service;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Drupal\sis_overview\Repository\TermRepository;
use Drupal\taxonomy\Entity\Term;

class TermContentDeliveryService {

  private TermRepository $termRepository;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  private EntityTypeManager $entityTypeManager;

  public function __construct(TermRepository $termRepository, EntityTypeManager $entityTypeManager) {
    $this->termRepository = $termRepository;
    $this->entityTypeManager = $entityTypeManager;
  }

  public function getEntitiesByTerm(Term $term, $limit = 0, $page = 0): array {
    if(!$nodeIds = $this->termRepository->getNodeIdsByTaxonomyTermIds($term->id(), $limit, $page)) {
      return [];
    }

    $nodes = Node::loadMultiple($nodeIds);
    $termNodes = $this->entityTypeManager
      ->getViewBuilder('node')
      ->viewMultiple($nodes, 'list');



    return [
      '#theme' => 'term_overview',
      '#entities' => $termNodes,
      '#term' => $term
    ];
  }
}
