<?php

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_alter().
 */
/**
 * Implements hook_form_BASE_FORM_ID_alter().
 */
function sis_form_node_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
  $article_forms = array(
    'node_article_form',
    'node_article_edit_form',
    'node_dot_on_map_form',
    'node_dot_on_map_edit_form',
    'node_blog_post_form',
    'node_blog_post_edit_form',
  );
  if (in_array($form_id, $article_forms)) {
    $form['#attached']['library'][] = 'sis/edit-form';
    unset($form['field_meta_tags']);
  }
}

/**
 * Implements hook_form_alter().
 */
function sis_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
  if ($form_id === 'entity_overview_filter_form') {
    $form["#attached"]["library"][] = 'sis/pager-overview';
  }
}
