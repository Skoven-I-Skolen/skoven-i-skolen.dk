<?php

function sis_blog_post_preprocess_block__inline_block__blog_list(&$variables) {

  $max_elements = 4;

  if (isset($variables['elements']['content']['#block_content'])) {
    if ($value = $variables['elements']['content']['#block_content']->get('field_max_elements')->value) {
      $max_elements = $value;
    }
  }

  $variables['latest_four_blog_posts'] = \Drupal::service('sis_blog_post.content_delivery_service')
    ->getBlogPosts($max_elements);
}
