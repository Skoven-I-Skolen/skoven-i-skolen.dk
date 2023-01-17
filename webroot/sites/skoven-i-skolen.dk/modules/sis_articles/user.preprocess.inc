<?php

function sis_articles_preprocess_user(&$variables) {
  $variables['articles_by_writer'] = \Drupal::service('sis_articles.content_delivery_service')
    ->getArticlesByUser($variables['user']->get('uid')->value);
  if (Drupal::currentUser()->id() === $variables['user']->get('uid')->value) {
    $variables['unpublished_content_by_author'] = \Drupal::service('sis_articles.content_delivery_service')
      ->getUnpublishedContentByUser($variables['user']->get('uid')->value);
  }
}
