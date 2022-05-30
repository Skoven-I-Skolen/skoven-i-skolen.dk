<?php

use Drupal\Core\Form\FormStateInterface;

function sis_organization_form_node_article_form_alter(&$form, FormStateInterface $form_state, $form_id) {

  $currentUser = \Drupal::currentUser();

  if (in_array('organization', $currentUser->getRoles())) {
    $form['field_header']['#access'] = FALSE;
    $form['revision_log']['#access'] = FALSE;

    $form['field_article_type']['widget']['#default_value'] = \Drupal::entityQuery('taxonomy_term')->condition('name', 'Aktivitet')->execute() ?? NULL;
    $form['field_article_type']['#access'] = FALSE;
  }
}
