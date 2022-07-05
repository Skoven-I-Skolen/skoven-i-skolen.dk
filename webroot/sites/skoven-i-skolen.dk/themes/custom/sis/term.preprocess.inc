<?php

function sis_preprocess_term_overview(&$variables) {
  $variables['description'] = [
    '#markup' => $variables['term']->get('description')->value,
  ];
}
