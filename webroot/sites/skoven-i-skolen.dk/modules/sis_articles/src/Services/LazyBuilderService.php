<?php

namespace Drupal\sis_articles\Services;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\node\Entity\Node;

class LazyBuilderService implements TrustedCallbackInterface {

  const ONE_DAY = 86400;

  /**
   * @param $node
   *
   * @return array
   */
  public static function getArticleInspirationContent($nodeId): array {
    $articleContentDeliveryService = \Drupal::service('sis_articles.content_delivery_service');
    $inspirations = $articleContentDeliveryService->getInspirationalArticlesForCurrent(Node::load($nodeId));

    $items = [];
    foreach ($inspirations as $title => $inspiration) {
      if ($inspiration == null) {
        continue;
      }
      $items[$title][] = \Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->viewMultiple($inspiration, 'simple_list');
    }

    return [
      '#theme' => 'factbox_items',
      '#items' => $items,
      '#cache' => [
        'max-age' => self::ONE_DAY,
        'context' => ['url.path']
      ]
    ];
  }

  /**
   * @param $node
   *
   * @return array
   */
  public static function getRelatedArticles($node): array {
    $articleContentDeliveryService = \Drupal::service('sis_articles.content_delivery_service');
    $related = $articleContentDeliveryService->getRelatedArticles($node);
    $items = \Drupal::entityTypeManager()
      ->getViewBuilder('node')
      ->viewMultiple($related, 'list');

    return $items;
  }

  /**
   * @inheritDoc
   */
  public static function trustedCallbacks() {
    return [
      'getArticleInspirationContent',
      'getRelatedArticles'
    ];
  }

}
