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
