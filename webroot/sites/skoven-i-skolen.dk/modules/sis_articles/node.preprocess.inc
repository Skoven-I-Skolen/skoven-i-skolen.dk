<?php

function sis_articles_preprocess_node(&$variables) {

  if ($variables['node']->bundle() === 'article' && $variables['view_mode'] === 'full') {
    /** @var \Drupal\sis_articles\Services\ArticleContentDeliveryService $contentDelivery */
    $contentDelivery = \Drupal::service('sis_articles.content_delivery_service');
    $inspirations = $contentDelivery->getInspirationalArticlesForCurrent($variables['node']);
    $related = $contentDelivery->getRelatedArticles($variables['node']);

    foreach ($inspirations as $title => $inspiration) {
      $variables['inspiration'][$title] = Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->viewMultiple($inspiration, 'default');
    }

    if ($related) {
      $variables['related'] = Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->viewMultiple($related, 'default');
    }
  }
}

