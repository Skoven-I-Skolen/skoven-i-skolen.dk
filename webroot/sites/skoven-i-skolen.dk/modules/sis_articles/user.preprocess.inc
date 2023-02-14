<?php

function sis_articles_preprocess_user(&$variables) {
  $variables['articles_by_writer'] = \Drupal::service('sis_articles.content_delivery_service')
    ->getPublishedContentByUser($variables['user']->get('uid')->value);
  $current_user = Drupal::currentUser();
  if ($current_user->id() === $variables['user']->get('uid')->value ||
      $current_user->hasPermission('view unpublished content')) {
    $variables['unpublished_content_by_author'] = \Drupal::service('sis_articles.content_delivery_service')
      ->getUnpublishedContentByUser($variables['user']->get('uid')->value);
  }
}
