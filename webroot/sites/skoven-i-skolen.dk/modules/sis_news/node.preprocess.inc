<?php

function sis_news_preprocess_node(&$variables) {

  if ($variables['node']->bundle() === 'news' && $variables['view_mode'] === 'full') {
    /** @var \Drupal\sis_news\Services\NewsContentDeliveryService $contentDelivery */
    $contentDelivery = \Drupal::service('sis_news.content_delivery_service');

    if ($latest = $contentDelivery->getLatestNews($variables['node'])) {
      $variables['latest'] = $latest;
    }
  }
}


function sis_news_preprocess_page(&$variables) {
  /** @var \Drupal\sis_almanac\Services\AlmanacContentDeliveryService $almanacContentDeliveryService */
  $newsContentDeliveryService = \Drupal::service('sis_news.content_delivery_service');
  $variables['latest_news'] = $newsContentDeliveryService->getLatestNewsAsSimplelist();
}
