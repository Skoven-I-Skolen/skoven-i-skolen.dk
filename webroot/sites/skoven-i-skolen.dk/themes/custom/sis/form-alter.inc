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
  if (in_array($form_id, array('node_article_form', 'node_article_edit_form'))) {
    $form['form_description'] = array(
      '#markup' => '<p>Her kan du dele en artikel. Er det en aktivitet, så skriv enkelt, så børn kan forstå det. Er det et undervisningsforløb, så skriv til lærere. Skriv kort og klart uanset hvad.</p>',
      '#weight' => -100,
    );
    $form['field_factboxes']['widget']['text']['value']['#prefix'] = '<p>Her kan du tilføje ekstra faktabokse, som bliver vist til højre for artiklen.</p>';
    $form['field_factboxes']['widget']['text']['value']['#prefix'] .= '<em>';
  }
  if (in_array($form_id, array('node_link_article_form', 'node_link_article_edit_form'))) {
    $form['form_description'] = array(
      '#markup' => '<p>Her kan du dele indhold fra en anden hjemmeside. Indholdet er tiltænkt som et undervisningsforløb til lærere. Skriv venligst kort og klart.</p>',
      '#weight' => -100,
    );
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
}

/**
 * Implements hook_form_BASE_FORM_ID_alter().
 */
function sis_form_user_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
  $roles = Drupal::currentUser()->getRoles();
  $form['field_summary']['#access'] = !in_array('organization', $roles, TRUE);
}
