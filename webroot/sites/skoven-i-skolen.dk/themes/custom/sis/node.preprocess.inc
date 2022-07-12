<?php

function sis_preprocess_node(&$variables) {
  /** @var \Drupal\node\Entity\Node $node */
  $node = $variables['node'];

  if ($node->hasField('field_header') && $node->get('field_header')
      ->isEmpty()) {
    $variables['content']['title'] = [
      '#theme' => 'page_title',
      '#title' => $node->get('title')->value,
      '#weight' => -100,
    ];
  }

  if (!$node && $request = \Drupal::requestStack()->getCurrentRequest()) {
    /** @var \Drupal\taxonomy\Entity\Term $term */
    if ($term = $request->get('taxonomy_term')) {
      if ($term->get('vid')->target_id === 'article_types') {
        $variables['content']['title'] = [
          '#theme' => 'page_title',
          '#title' => $term->label(),
        ];
      }

    }
  }
}

function sis_preprocess_page(&$variables) {
  /** @var \Drupal\node\Entity\Node $node */
  $node = $variables['node'] ?? NULL;

  if ($node && $node->hasField('field_header') && $node->get('field_header')
      ->isEmpty()) {
    unset($variables['page']['pageheader']);
  }

  if (!$node && $request = \Drupal::requestStack()->getCurrentRequest()) {
    $uri = $request->getPathInfo();

    $url_filter_list = ['/organisationer','/search'];
    if (in_array($uri, $url_filter_list)) {
      unset($variables['page']['pageheader']);
    }

    if (substr('/node/add/article', 0,9) === '/node/add') {
      $variables['page']['pageheader']['#attributes']['class'][] = 'node-edit-form__title';
      unset($variables['page']['breadcrumb']);
    }

    /** @var \Drupal\taxonomy\Entity\Term $term */
    if ($term = $request->get('taxonomy_term')) {
      unset($variables['page']['pageheader']);
    }
  }

  $variables['footer_col1_header'] = atom_str('footer-col1-header');


  $variables['footer_col2_header'] = atom_str('footer-col2-header');

  $variables['facebook_link'] = atom_str('facebook-link');
  $variables['instagram_link'] = atom_str('instagram-link');

  $variables['footer_col2_text'] = atom_str('footer-col2-text');
  $variables['col_2_button_name'] = atom_str('col-2-button-name');
  $variables['col_2_button_link'] = atom_str('col-2-button-link');
}
