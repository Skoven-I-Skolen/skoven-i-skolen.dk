<?php

function sis_preprocess_node(&$variables) {
  /** @var \Drupal\node\Entity\Node $node */
  $node = $variables['node'];

  $hasHeader = FALSE;
  if (!$node->get('field')->isEmpty()) {
    $hasHeader = TRUE;
  }

  $variables['has_header'] = $hasHeader;
}
