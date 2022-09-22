<?php

function sis_blog_post_preprocess_node(&$variables) {
  if ($variables['node']->get('type')->getString() === 'blog_post') {
    $author = $variables['node']->getOwner();

    if ($variables['view_mode'] === 'full') {

      $all_writers = \Drupal::service('sis_blog_post.content_delivery_service')
        ->getWriters(NULL, NULL);

      $variables['all_writers'] = $all_writers;
      $variables['current_writer'] = NULL;
      $variables['other_writers'] = [];

      foreach ($all_writers as $writer) {
        if ($user = $writer['#user']) {
          if ($user === $author) {
            $variables['current_writer'] = $writer;
          }
          else {
            $variables['other_writers'][] = $writer;
          }
        }
      }
      $current_node = $variables['node']->get('nid')->getString();
      $variables['other_blog_posts'] = \Drupal::service('sis_blog_post.content_delivery_service')
        ->getBlogPostsByAuthor([$variables['node']->getOwnerId()], $current_node);
    }

    $creation_date = \Drupal::service('date.formatter')
      ->format($variables['node']->getCreatedTime(), 'custom', 'd M Y');
    $full_name = $author->hasField('field_full_name') ? $author->get('field_full_name')->getString() : $author->get('name')->getString();
    $authoring_information = t('Af ') . $full_name . ' | ' . strtolower($creation_date);
    $variables['authoring_information'] = [
      '#type' => 'markup',
      '#markup' => $authoring_information,
    ];
  }
}
