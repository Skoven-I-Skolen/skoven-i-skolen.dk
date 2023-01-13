<?php

namespace Drupal\sis_blog_post\Repository;

use Drupal\node\NodeInterface;

class BlogPostRepository {

  public function getWriters($target_id, $exception): array {
    $writers = \Drupal::entityQuery('user')
      ->condition('roles', 'writer', 'CONTAINS');

    if ($target_id) {
      $writers->condition('uid', $target_id, '=');
    }

    if ($exception) {
      $writers->condition('uid', $exception, '!=');
    }

    $writers->sort('field_full_name', 'ASC');

    return $writers->execute();
  }

  public function getBlogPosts($limit) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'blog_post')
      ->condition('status', NodeInterface::PUBLISHED)
      ->range(0, $limit)
      ->sort('created', 'DESC');
    return $query->execute();
  }

  public function getBlogPostsByAuthor($author_ids) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'blog_post')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('uid', $author_ids)
      ->range(0, 12)
      ->sort('created', 'DESC');
    return $query->execute();
  }
}
