<?php

namespace Drupal\sis_news\Services;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\sis_news\Repository\NewsRepository;

class NewsContentDeliveryService {

  /**
   * @var \Drupal\sis_news\Repository\NewsRepository
   */
  private NewsRepository $newsRepository;

  /**
   * @var \Drupal\Core\Entity\EntityViewBuilder
   */
  private EntityViewBuilder $entityViewBuilder;

  public function __construct(NewsRepository $newsRepository, EntityTypeManager $entityTypeManager) {
    $this->newsRepository = $newsRepository;
    $this->entityViewBuilder = $entityTypeManager->getViewBuilder('node');
  }

  /**
   * Get Latest news
   *
   * @return array
   *   Array of rendered news items.
   */
  public function getLatestNews(?NodeInterface $node): array {
    if ($latest = $this->newsRepository->getLatestNews($node)) {
      $nodes = Node::loadMultiple($latest);
      return $this->entityViewBuilder->viewMultiple($nodes,'list');
    }

    return [];
  }

  /**
   * Get Latest news
   *
   * @return array
   *   Array of rendered news items.
   */
  public function getLatestNewsAsSimplelist(): array {
    if ($latest = $this->newsRepository->getLatestNews(NULL, 3)) {
      $nodes = Node::loadMultiple($latest);
      return $this->entityViewBuilder->viewMultiple($nodes,'simple_list');
    }

    return [];
  }

  /**
   * Get Latest news
   *
   * @return array
   *   Array of rendered news items.
   */
  public function getLatestContentAsSimplelist(): array {
    if ($latest = $this->newsRepository->getLatestContent(NULL, 3)) {
      $nodes = Node::loadMultiple($latest);
      return $this->entityViewBuilder->viewMultiple($nodes,'simple_list');
    }

    return [];
  }
}
