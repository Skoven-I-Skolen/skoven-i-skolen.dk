<?php

function sis_preprocess_node(&$variables) {
  /** @var \Drupal\node\Entity\Node $node */
  $node = $variables['node'];

  $hasHeader = FALSE;
  if ($node->hasField('field_header') && !$node->get('field_header')->isEmpty()) {
    $hasHeader = TRUE;
  }

  $variables['has_header'] = $hasHeader;
}
