<?php

/**
 * Basic hero preprocess.
 *
 * @param array $variables
 *   Template variables.
 */
function sis_preprocess_paragraph__basic_hero(array &$variables) {
  /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
  $paragraph = $variables['elements']['#paragraph'];

  if ($paragraph->hasField('field_background') && !$paragraph->get('field_background')
      ->isEmpty()) {
    $media = $paragraph->get('field_background')->entity;

    if (!is_null($media)) {
      if ($media->bundle() == 'image') {
        if ($paragraph->hasField('field_dynamic_height') && (int) $paragraph->get('field_dynamic_height')->value === 1) {
          $variables['media_image'] = \Drupal::entityTypeManager()
            ->getViewBuilder('media')
            ->view($media, 'full_width_flex_height');
        }
      }
    }
  }
}

/**
 * Inline hero preprocess.
 *
 * @param array $variables
 *   Template variables.
 */
function sis_preprocess_paragraph__inline_hero(array &$variables) {
  /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
  $paragraph = $variables['elements']['#paragraph'];

  if ($paragraph->hasField('field_background') && !$paragraph->get('field_background')
      ->isEmpty()) {
    $media = $paragraph->get('field_background')->entity;

    if (!is_null($media)) {
      if ($media->bundle() == 'image') {
        if ($paragraph->hasField('field_dynamic_height') && (int) $paragraph->get('field_dynamic_height')->value === 1) {
          $variables['media_image'] = \Drupal::entityTypeManager()
            ->getViewBuilder('media')
            ->view($media, 'full_width_flex_height');
        }
      }
    }
  }
}
