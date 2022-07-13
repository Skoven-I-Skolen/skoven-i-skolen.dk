<?php

namespace Drupal\sis_blog_post\Repository;

use Drupal\file\Entity\File;
use Drupal\user\Entity\User;

class BlogPostRepository {

  public function getWriters($target_id, $exception): array {
    $writers = \Drupal::entityQuery('user')
      ->condition('roles', 'writer', 'CONTAINS');

    if ($target_id) {
      $writers->condition('uid', $target_id, '=');
    }

    if ($exception) {
      $writers->condition('uid', $exception, '!=');
    }
    $writers = $writers->execute();

    $writers_array = [];
    foreach ($writers as $writer) {
      $writer = User::load($writer);
      $writers_array[$writer->id()]['url'] = $writer->toUrl()->toString();
      if ($writer->hasField('field_full_name')) {
        $writers_array[$writer->id()]['field_full_name'] = $writer->get('field_full_name')->getString();
      }
      if ($writer->hasField('field_summary')) {
        $writers_array[$writer->id()]['field_summary'] = $writer->get('field_summary')->getString();
      }
      if ($writer->hasField('user_picture')) {
        $media_id = NULL;
        if ($writer->get('user_picture')->first()) {
          $media_id = $writer->get('user_picture')->first()->target_id;
        }
        if ($media_id) {
          $file = File::load($media_id);
          if ($file) {
            $writers_array[$writer->id()]['user_picture'] = $file->createFileUrl();
          }
        }
      }
    }
    return $writers_array;
  }
}
