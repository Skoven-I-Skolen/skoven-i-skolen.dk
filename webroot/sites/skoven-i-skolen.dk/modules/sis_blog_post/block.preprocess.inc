<?php

function sis_blog_post_preprocess_block__inline_block__blog_list(&$variables) {

  $max_elements = 4;

  if (isset($variables['elements']['content']['#block_content'])) {
    if ($value = $variables['elements']['content']['#block_content']->get('field_max_elements')->value) {
      $max_elements = $value;
    }
  }
  $variables['latest_four_blog_posts_count'] = $max_elements;
  $variables['latest_four_blog_posts'] = \Drupal::service('sis_blog_post.content_delivery_service')
    ->getBlogPosts($max_elements);
}

function sis_blog_post_preprocess_block__inline_block__bloggers(array &$variables) {
  $all_writers = \Drupal::service('sis_blog_post.content_delivery_service')
    ->getWriters(NULL, NULL);

  // sort by name
  usort($all_writers, function ($a, $b) {
    $name_a = $a['#user']->name->value;
    $name_b = $b['#user']->name->value;

    if (!$a['#user']->get('field_full_name')->isEmpty()) {
      $name_a = $a['#user']->field_full_name->value;
    }
    if (!$b['#user']->get('field_full_name')->isEmpty()) {
      $name_b = $b['#user']->field_full_name->value;
    }

    return strcmp($name_a, $name_b);
  });

  $variables['all_writers'] = $all_writers;
}
