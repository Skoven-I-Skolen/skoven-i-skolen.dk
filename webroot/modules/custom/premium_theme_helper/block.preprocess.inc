<?php

use Drupal\Core\Render\Element;

function premium_theme_helper_preprocess_block__inline_block__entity_slideshow(&$variables) {

  /** @var \Drupal\premium_theme_helper\Service\EntitySlideshowContentDeliveryService $content_delivery_service */
  $content_delivery_service = \Drupal::service('premium_theme_helper.content_delivery_service');
  /** @var \Drupal\block_content\Entity\BlockContent $block_content */
  $block_content = $variables['elements']['content']['#block_content'];

  $nodes = $content_delivery_service->getEntitiesForSlideshow($block_content);

  foreach (Element::children($nodes) as $key) {
    if($nodes[$key]['#node']->access()) {
      $variables['items'][] = $nodes[$key];
    }
  }
}
