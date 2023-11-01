<?php

namespace Drupal\sis_news\Repository;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\NodeInterface;

class NewsRepository {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  private EntityTypeManager $entityTypeManager;

  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Get the latest news.
   *
   * @param \Drupal\node\NodeInterface|NULL $node
   *   The node to filter from the result.
   *
   * @return array
   *   Array if node ids.
   */
  public function getLatestNews(NodeInterface $node = NULL, $limit = 12): array {
    $query = \Drupal::entityQuery('node')->accessCheck(FALSE)
      ->condition('type', 'news')
      ->condition('status', NodeInterface::PUBLISHED)
      ->range(0, $limit)
      ->sort('created', 'DESC');


    // If node is provided, filter if from the result.
    if ($node) {
      $query->condition('nid', $node->id(), '!=');
    }
    return $query->execute();
  }

  /**
   * Get the latest content.
   *
   * @param \Drupal\node\NodeInterface|NULL $node
   *   The node to filter from the result.
   *
   * @return array
   *   Array if node ids.
   */
  public function getLatestContent(NodeInterface $node = NULL, $limit = 12): array {
    $query = \Drupal::entityQuery('node')->accessCheck(FALSE)
      ->condition('type', ['news', 'article', 'blog_post'], 'IN')
      ->condition('status', NodeInterface::PUBLISHED)
      ->range(0, $limit)
      ->sort('created', 'DESC');


    // If node is provided, filter if from the result.
    if ($node) {
      $query->condition('nid', $node->id(), '!=');
    }
    return $query->execute();
  }

}
