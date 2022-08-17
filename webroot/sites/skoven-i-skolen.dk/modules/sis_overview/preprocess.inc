<?php

/**
 * Implements HOOK_preprocess()
 *
 * Add is-ajax class to pager links.
 *
 * @param $variables
 *   The variables array to alter.
 *
 * @return void
 */
function sis_overview_preprocess_pager(&$variables) {
//  if (isset($variables['pager'])) {
//    if (isset($variables['items']) && count($variables['items'])) {
//      foreach ($variables['items'] as $key => $value) {
//        if (!empty($value['attributes'])) {
//          $variables['items'][$key]['attributes']->addClass('use-ajax');
//        }
//        else {
//          foreach ($value as $subKey => $subValue) {
//            if (!empty($subValue['attributes'])) {
//              $variables['items'][$key][$subKey]['attributes']->addClass('use-ajax');
//            }
//          }
//        }
//      }
//    }
//  }
}


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
