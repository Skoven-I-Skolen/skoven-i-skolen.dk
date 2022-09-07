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
  $route_name = $variables['pager']['#route_name'];
  if ($route_name !== 'sis_overview.term.get') {
    return;
  }

  if (isset($variables['pager'])) {
    if (isset($variables['items']) && count($variables['items'])) {
      foreach ($variables['items'] as $key => $value) {
        if (!empty($value['attributes'])) {
          $variables['items'][$key]['attributes']->addClass('use-ajax');
        }
        else {
          foreach ($value as $subKey => $subValue) {
            if (!empty($subValue['attributes'])) {
              $variables['items'][$key][$subKey]['attributes']->addClass('use-ajax');
            }
          }
        }
      }
    }
  }
}


function sis_overview_preprocess_term_overview(&$variables) {
  $breadcrumb = getBreadcrumb();
  return $variables['breadcrumb'] = $breadcrumb;
}

function sis_overview_preprocess_taxonomy_term(&$variables) {
  $breadcrumb = getBreadcrumb();

  /**
   * Render the desciption, and add it to the content array.
   *
   * If the layout builder is enables. It's only possible to print field using
   * the layout builder. The description should be showen just below the main title
   * which is not easy the control with the layout builder.
   */
  if (isset($variables['content']['_layout_builder'])) {
    $term = $variables['term'];
    $variables['content']['description'] = $term->get('description')->view([
      'label' => 'hidden',
    ]);
  }

  /**
   * @see Drupal\sis_overview\Breadcrumb\TermBreadcrumbBuilder
   */
  return $variables['breadcrumb'] = $breadcrumb;
}

/**
 * @return mixed
 */
function getBreadcrumb() {
  $block_manager = \Drupal::service('plugin.manager.block');

  // You can hard code configuration or you load from settings.
  $config = [];
  $plugin_block = $block_manager->createInstance('premium_breadcrumb_block', $config);
  $breadcrumb = $plugin_block->build();

  // Add the cache tags/contexts.
  \Drupal::service('renderer')
    ->addCacheableDependency($breadcrumb, $plugin_block);
  return $breadcrumb;
}
