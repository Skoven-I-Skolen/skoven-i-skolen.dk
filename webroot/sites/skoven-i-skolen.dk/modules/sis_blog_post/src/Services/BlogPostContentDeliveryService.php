<?php

namespace Drupal\sis_blog_post\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\node\Entity\Node;
use Drupal\sis_blog_post\Repository\BlogPostRepository;
use Drupal\user\Entity\User;

class BlogPostContentDeliveryService {

  /** @var BlogPostRepository $blogPostRepository */
  protected $blogPostRepository;

  /**
   * @var \Drupal\Core\Entity\EntityViewBuilder
   */
  private EntityTypeManagerInterface $entityTypeManager;

  public function __construct(BlogPostRepository $blogPostRepository, EntityTypeManagerInterface $entityTypeManager) {
    $this->blogPostRepository = $blogPostRepository;
    $this->entityTypeManager = $entityTypeManager;
  }

  public function getBlogPosts($limit) {
    if ($results = $this->blogPostRepository->getBlogPosts($limit)) {
      $posts = Node::loadMultiple($results);
      return $this->entityTypeManager->getViewBuilder('node')->viewMultiple($posts, 'list');
    }
  }

  public function getWriters($target_id, $exception): array {
    $users_view = [];
    if ($results = $this->blogPostRepository->getWriters($target_id, $exception)) {
      $users = User::loadMultiple($results);
      foreach ($users as $user) {
        $users_view[] = $this->entityTypeManager->getViewBuilder('user')->view($user, 'compact');
      }
      return $users_view;
    }
    return [];
  }

  public function getBlogPostsByAuthor($author_ids, $current_node = NULL) {
    if ($posts = $this->blogPostRepository->getBlogPostsByAuthor($author_ids)) {
      $nodes = Node::loadMultiple($posts);
      if ($current_node) {
        unset($nodes[$current_node]);
      }
      return $this->entityTypeManager->getViewBuilder('node')->viewMultiple($nodes, 'list');
    }
    return [];
  }
}
