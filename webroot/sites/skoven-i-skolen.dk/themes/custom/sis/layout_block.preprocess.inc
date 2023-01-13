<?php

use Drupal\node\Entity\Node;

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

function sis_preprocess_block__inline_block__button(array &$variables) {
  /** @var \Drupal\block_content\Entity\BlockContent $block_content */
  $block_content = $variables['content']['#block_content'];

  if(!$block_content->get('field_button_alignment')->isEmpty()) {
    $variables['attributes']['class'][] = 'button-alignment--' .$block_content->get('field_button_alignment')->value;
  }
}

/**
 * Entity slideshow layout block preprocess.
 *
 * @param array $variables
 *   Template variables.
 */
function sis_preprocess_block__inline_block__entity_slideshow(array &$variables) {
  /** @var \Drupal\block_content\Entity\BlockContent $block */
  $block = $variables['elements']['content']['#block_content'];
  $nodes = [];
  $node_ids = [];
  if ($block->hasField('field_entity_slideshow_items')) {
    foreach ($block->get('field_entity_slideshow_items') as $value) {
      $node_ids[] = $value->target_id;
    }
    $nodes = Node::loadMultiple($node_ids);
  }

  if (!empty($nodes)) {
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
    $rendered_nodes = [];
    /** @var \Drupal\node\Entity\Node $node */
    foreach ($nodes as $node) {
      if ($node->access()) {
        $rendered_nodes[] = $view_builder->view($node, 'slideshow');
      }
    }
    $variables['manually_selected_nodes'] = $rendered_nodes;
  }
  else {
    $variables['items'] = [];
  }
}

function sis_preprocess_block__inline_block__news_overview(array &$variables) {
  $show_four_items = (bool) $variables['content']['#block_content']->get('field_show_four_items')->first()->value;
  if ($show_four_items) {
    $node = \Drupal::routeMatch()->getParameter('node');
    $latest_news = Drupal::service('sis_news.content_delivery_service')->getLatestNews($node);
    $variables['terms_4'] = $latest_news;
  }
  else {
    $news_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
      ->loadByProperties(['machine_name' => 'news']);
    if (!$news_term) {
      return;
    }
    $variables['terms_16'] = Drupal::service('sis_overview.term_overview')
      ->view(array_shift($news_term), TRUE);
    $variables['terms_16']['pager']['#route_parameters']['small'] = TRUE;
  }
}

/**
 * Slideshow layout block preprocess.
 *
 * @param array $variables
 *   Template variables.
 */
function sis_preprocess_block__inline_block__slideshow(array &$variables) {
  /** @var \Drupal\block_content\Entity\BlockContent $block */
  $block = $variables['elements']['content']['#block_content'];

  if ($block->hasField('field_slideshow_images') && !$block->get('field_slideshow_images')->isEmpty()) {
    $medias = $block->get('field_slideshow_images')->referencedEntities();
    $media_view_builder = \Drupal::entityTypeManager()->getViewBuilder('media');

    if (!empty($medias)) {
      $layout = $variables['elements']['content']['#layout'] ?? [];
      $layout_id = $layout['layout_id'] ?? '';
      switch ($layout_id) {
        case 'layout_threecol_section':
          $view_mode = 'slideshow_threeocol';
          break;

        case 'layout_twocol_section':
          $view_mode = 'slideshow_twocol';
          break;

        default:
          $view_mode = 'slideshow';
      }
      $variables['items'] = [];
      foreach ($medias as $media) {
        $variables['items'][] = ['image' => $media_view_builder->view($media, $view_mode)];
      }
    }
  }
}
