<?php

use Drupal\Core\Form\FormStateInterface;

function sis_organization_form_node_article_form_alter(&$form, FormStateInterface $form_state, $form_id) {

  $currentUser = \Drupal::currentUser();

  if (in_array('organization', $currentUser->getRoles())) {
    // $form['field_header']['#access'] = FALSE;
    $form['revision_log']['#access'] = FALSE;

    $type_from_url = Drupal::requestStack()->getCurrentRequest()->get('type');
    if (!$type_from_url) {
      $type_from_url = 'Aktivitet';
    }
    $form['field_article_type']['widget']['#default_value'] = \Drupal::entityQuery('taxonomy_term')->condition('name', $type_from_url)->execute() ?? NULL;
    foreach ($form['field_article_type']['widget']['#options'] as $key => $value) {
      if ($value !== 'Aktivitet' && $value !== 'Undervisningsforløb') {
        unset($form['field_article_type']['widget']['#options'][$key]);
      }
    }
  }
}
