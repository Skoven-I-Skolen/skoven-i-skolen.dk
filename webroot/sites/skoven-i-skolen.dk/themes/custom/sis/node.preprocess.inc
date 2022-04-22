<?php

function sis_preprocess_node(&$variables) {
  /** @var \Drupal\node\Entity\Node $node */
  $node = $variables['node'];

  if ($node->hasField('field_header') && $node->get('field_header')->isEmpty()) {
    $variables['content']['title'] = [
      '#theme' => 'page_title',
      '#title' => $node->get('title')->value,
    ];
  }
}

function sis_preprocess_page(&$variables) {
  /** @var \Drupal\node\Entity\Node $node */
  $node = $variables['node'];

  if ($node->hasField('field_header') && $node->get('field_header')->isEmpty()) {
    unset($variables['page']['pageheader']);
  }
}
