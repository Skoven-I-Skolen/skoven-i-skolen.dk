<?php

use Drupal\sis_articles\Services\LazyBuilderService;

function sis_articles_preprocess_node(&$variables) {

  if (($variables['node']->bundle() === 'article' ||  $variables['node']->bundle() === 'news' )
    && $variables['view_mode'] === 'full') {
    /** @var \Drupal\sis_articles\Services\ArticleContentDeliveryService $contentDelivery */
    $contentDelivery = \Drupal::service('sis_articles.content_delivery_service');
    $related = $contentDelivery->getRelatedArticles($variables['node']);

    $variables['inspirations'] = [
      '#lazy_builder' => [
        LazyBuilderService::class . '::getArticleInspirationContent', [
          $variables['node']->id(),
        ]
      ],
    ];

    if ($related) {
      $variables['related'] = Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->viewMultiple($related, 'list');
      $variables['related_count'] = count($related);
    }
  }
}

