<?php

namespace Drupal\sis_blog_post\Services;

use Drupal\sis_blog_post\Repository\BlogPostRepository;

class BlogPostContentDeliveryService {

  /** @var BlogPostRepository $blogPostRepository */
  protected $blogPostRepository;

  public function __construct(BlogPostRepository $blogPostRepository) {
    $this->blogPostRepository = $blogPostRepository;
  }

  public function getWriters($target_id, $exception): array {
    return $this->blogPostRepository->getWriters($target_id, $exception);
  }

}
