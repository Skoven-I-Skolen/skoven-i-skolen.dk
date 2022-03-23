<?php

namespace Drupal\sis_articles\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\sis_articles\Services\ArticleContentDeliveryService;

class DebugController extends ControllerBase {

  private ArticleContentDeliveryService $articleContentDeliveryService;

  public function __construct(ArticleContentDeliveryService $articleContentDeliveryService) {
    $this->articleContentDeliveryService = $articleContentDeliveryService;
  }

  public function debug() {
    $node = Node::load(4);
    dump($this->articleContentDeliveryService->getInspirationalArticlesForCurrent($node));

    return [];
  }

  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
    return new static(
      $container->get('sis_articles.content_delivery_service')
    );
  }


}
