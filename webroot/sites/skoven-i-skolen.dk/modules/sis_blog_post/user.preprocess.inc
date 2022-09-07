<?php

function sis_blog_post_preprocess_user(&$variables) {
  $variables['blog_posts_by_writer'] = \Drupal::service('sis_blog_post.content_delivery_service')
    ->getBlogPostsByAuthor([$variables['user']->get('uid')->value]);
}
