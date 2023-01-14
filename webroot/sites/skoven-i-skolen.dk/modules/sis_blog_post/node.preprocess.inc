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

      foreach ($all_writers as $key => $value) {
        if ($user = $value['#user']) {
          if ($user === $author) {
            $variables['current_writer'] = $value;
            unset($all_writers[$key]);
          }
        }
      }

      if (count($all_writers) > 1) {
        $random_authors = array_rand($all_writers, 2);
        $variables['other_writers'] = [
          $all_writers[$random_authors[0]],
          $all_writers[$random_authors[1]],
        ];
      }

      $variables['other_blog_posts'] = \Drupal::service('sis_blog_post.content_delivery_service')
        ->getBlogPostsByAuthor([$variables['node']->getOwnerId()]);

      $other_blog_posts_count = 0;
      foreach ($variables['other_blog_posts'] as $key => $blog_post) {
        if (is_numeric($key)) {
          $other_blog_posts_count++;
        }
      }
      $variables['other_blog_posts_count'] = $other_blog_posts_count;
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
