<?php

function sis_preprocess_pager(&$variables) {
  $element = $variables['pager']['#element'];
  /** @var \Drupal\Core\Pager\PagerManagerInterface $pager_manager */
  $pager_manager = \Drupal::service('pager.manager');

  $pager = $pager_manager->getPager($element);

  if ($pager_max = $pager->getTotalPages()) {
    $variables['items']['last']['page_number'] = $pager_max;
  }
}
