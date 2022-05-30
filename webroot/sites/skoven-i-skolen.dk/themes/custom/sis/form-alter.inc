<?php

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_alter().
 */
/**
 * Implements hook_form_BASE_FORM_ID_alter().
 */
function sis_form_node_article_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
  $form['#attached']['library'][] = 'sis/edit-form';
}
