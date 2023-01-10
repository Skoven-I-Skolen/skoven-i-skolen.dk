<?php

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_alter().
 */
/**
 * Implements hook_form_BASE_FORM_ID_alter().
 */
function sis_form_node_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  $article_forms = array(
    'node_article_form',
    'node_article_edit_form',
    'node_dot_on_map_form',
    'node_dot_on_map_edit_form',
    'node_blog_post_form',
    'node_blog_post_edit_form',
  );
  if (in_array($form_id, $article_forms)) {
    unset($form['field_meta_tags']);
    $form['revision_log']['#access'] = FALSE;
    $form['#attached']['library'][] = 'sis/edit-form';
  }
}

/**
 * Implements hook_form_alter().
 */
function sis_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  if ($form_id === 'entity_overview_filter_form') {
    $form['search']['title']['#prefix'] = "<h3>";
    $form['search']['title']['#suffix'] = "</h3>";
    $form["#attached"]["library"][] = 'sis/pager-overview';
  }
  else if ($form_id === 'entity_overview_filter_blog_form') {
//    print "<pre>" . print_r(array_keys($form), true) . "</pre>";
//    print "<pre>" . print_r($form['overview_content_id'], true) . "</pre>";
  }
}
