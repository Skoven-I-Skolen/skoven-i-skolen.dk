<?php

/**
 * Implements THEME_preprocess_block__inline_block__ID
 *
 * @param $variables
 */
function sis_map_preprocess_block__inline_block__map(&$variables) {
  $block_manager = \Drupal::service('plugin.manager.block');
  $plugin_block = $block_manager->createInstance('map_block', []);
  $render = $plugin_block->build();
  $variables['map'] = $render;
}
