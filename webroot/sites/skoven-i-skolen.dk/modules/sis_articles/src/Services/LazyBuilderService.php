<?php

namespace Drupal\sis_articles\Services;

use Drupal\Core\Security\TrustedCallbackInterface;

class LazyBuilderService implements TrustedCallbackInterface {

  /**
   * @param $node
   *
   * @return array
   */
  public static function getArticleInspirationContent($node): array {
    $articleContentDeliveryService = \Drupal::service('sis_articles.content_delivery_service');
    $inspirations = $articleContentDeliveryService->getInspirationalArticlesForCurrent($node);

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
        'max-age' => 3600,
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

    return [
      '#theme' => 'factbox_items',
      '#items' => $items,
      '#cache' => [
        'max-age' => 3600,
        'context' => ['url.path']
      ]
    ];
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
