<?php

function sis_overview_preprocess_term_overview(&$variables) {
  $block_manager = \Drupal::service('plugin.manager.block');

  // You can hard code configuration or you load from settings.
  $config = [];
  $plugin_block = $block_manager->createInstance('premium_breadcrumb_block', $config);
  $breadcrumb = $plugin_block->build();

  // Add the cache tags/contexts.
  \Drupal::service('renderer')->addCacheableDependency($breadcrumb, $plugin_block);
  return $variables['breadcrumb'] = $breadcrumb;
}
