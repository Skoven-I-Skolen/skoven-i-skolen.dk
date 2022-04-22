<?php

/**
 * Appetizer layout block preprocess.
 *
 * @param array $variables
 *   Template variables.
 */
function sis_preprocess_block__inline_block__appetizer(array &$variables) {
  /** @var \Drupal\block_content\Entity\BlockContent $block */
  $block = $variables['elements']['content']['#block_content'];
  $layout = $variables['elements']['content']['#layout'] ?? [];
  $layout_id = $layout['layout_id'] ?? '';
  $image_style = '1_column_flex_height';

  if ($layout_id === 'layout_onecol' && isset($layout['settings']['column_width']) && $layout['settings']['column_width'] === 'fullwidth') {
    $image_style = '2_column_flex_height';
    $variables['display_as_fullwidth'] = TRUE;
  }
  elseif ($layout_id === 'layout_onecol') {
    $image_style = '1_column_flex_height';
  }
  elseif ($layout_id === 'layout_twocol_section') {
    $image_style = '2_column_flex_height';
  }
  elseif ($layout_id === 'layout_threecol_section') {
    $image_style = '3_column_flex_height';
  }

  if ($block->hasField('field_appetizer_image') && !$block->get('field_appetizer_image')->isEmpty() && !is_null($block->get('field_appetizer_image')->entity)) {
    $variables['media'] = \Drupal::entityTypeManager()->getViewBuilder('media')->view($block->get('field_appetizer_image')->entity, $image_style);
  }
}
