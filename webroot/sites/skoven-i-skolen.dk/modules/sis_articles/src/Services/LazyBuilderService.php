<?php

namespace Drupal\sis_articles\Services;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Plugin\views\argument\Taxonomy;

class LazyBuilderService implements TrustedCallbackInterface {

  const ONE_DAY = 86400;

  /**
   * @param $node
   *
   * @return array
   */
  public static function getArticleInspirationContent($nodeId): array {
    if (empty($nodeId)) return [];
    $articleContentDeliveryService = \Drupal::service('sis_articles.content_delivery_service');
    $node = Node::load($nodeId);
    if ($type_term = $node->hasField('field_article_type')) {
      $article_type = $node->get('field_article_type')->getString();
      $type_term = Term::load($article_type);
    }
    if ($type_term) {
      $type_term = $type_term->get('machine_name')->getString();
    }
    if ($type_term === 'lexicon') {
      $inspirations = $articleContentDeliveryService->getRandomInspirationalArticles();
    }
    else {
      $inspirations = $articleContentDeliveryService->getInspirationalArticlesForCurrent($node);
    }

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
