<?php

use Drupal\Core\Render\Element;

function sis_articles_preprocess_block__inline_block__entity_slideshow(&$variables) {
  $content_delivery_service = \Drupal::service('sis_articles.content_delivery_service');

  /** @var \Drupal\block_content\Entity\BlockContent $block */
  $block = $variables['elements']['content']['#block_content'];
  $target_id = $block->get('field_entity_slideshow_term')->target_id;

  if($term = \Drupal::request()->get('taxonomy_term')) {
    $target_id = $term->id();
  }

  $articles = $content_delivery_service->getArticlesByType($target_id, 4, 'slideshow');

  foreach (Element::children($articles) as $key) {
    $variables['items'][] = $articles[$key];
  }
}
