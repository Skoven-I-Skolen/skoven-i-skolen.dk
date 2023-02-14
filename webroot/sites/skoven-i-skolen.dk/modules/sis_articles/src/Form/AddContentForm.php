<?php

namespace Drupal\sis_articles\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AddContentForm extends FormBase {

  public function getFormId() {
    return 'add_content';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = [
      '<strong>' . atom_str('share-page-first-option-title') .
      '</strong><div class="">' . atom_str('share-page-first-option-help-text') . '</div>',
      '<strong>' . atom_str('share-page-second-option-title') .
      '</strong><div class="">' . atom_str('share-page-second-option-help-text') . '</div>',
      '<strong>' . atom_str('share-page-third-option-title') .
      '</strong><div class="">' . atom_str('share-page-third-option-help-text') . '</div>',
      '<strong>' . atom_str('share-page-fourth-option-title') .
      '</strong><div class="">' . atom_str('share-page-fourth-option-help-text') . '</div>',
    ];

    $form['fieldset'] = [
      '#type' => 'fieldset',
      '#title' => atom_str('share-page-title'),
    ];
    $form['fieldset'][] = [
      '#type' => 'markup',
      '#markup' => atom_str('share-page-first-fieldset-title'),
      '#prefix' => '<h4 class="fieldset-description">',
      '#suffix' => '</h4>',
    ];
    $form['fieldset'][] = [
      '#type' => 'markup',
      '#markup' => atom_str('share-page-first-fieldset-suffix'),
      '#prefix' => '<p class="fieldset-description">',
      '#suffix' => '</p>',
    ];
    $form['fieldset']['radio_group_1'] = [
      '#type' => 'checkboxes',
      '#options' => [],
    ];
    $form['fieldset'][] = [
      '#type' => 'markup',
      '#markup' => atom_str('share-page-second-fieldset-title'),
      '#prefix' => '<h4 class="fieldset-description">',
      '#suffix' => '</h4>',
    ];
    $form['fieldset'][] = [
      '#type' => 'markup',
      '#markup' => atom_str('share-page-second-fieldset-suffix'),
      '#prefix' => '<p class="fieldset-description">',
      '#suffix' => '</p>',
    ];
    $form['fieldset']['radio_group_2'] = [
      '#type' => 'checkboxes',
      '#options' => [],
    ];

    $form['fieldset']['radio_group_1']['#options'][] = $options[0];
    $form['fieldset']['radio_group_2']['#options'][] = $options[1];
    $form['fieldset']['radio_group_2']['#options'][] = $options[2];
    if (
      in_array('administrator', \Drupal::currentUser()->getRoles()) ||
      in_array('webmaster', \Drupal::currentUser()->getRoles()) ||
      in_array('editor', \Drupal::currentUser()->getRoles()) ||
      in_array('organization', \Drupal::currentUser()->getRoles())) {
      $form['fieldset']['radio_group_2']['#options'][] = $options[3];
    }

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Continue'),
    );

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $selected = NULL;
    $input = $form_state->getUserInput();
    if (isset($input['radio_group_1'][0]) &&
      $input['radio_group_1'][0] !== NULL) {
      $selected = 'dot';
    }
    else if (isset($input['radio_group_2'][0]) &&
      $input['radio_group_2'][0] != NULL) {
      $selected = 'article';
    }
    else if (isset($input['radio_group_2'][1]) &&
      $input['radio_group_2'][1] != NULL) {
      $selected = 'education';
    }
    else if (isset($input['radio_group_2'][2]) &&
      $input['radio_group_2'][2] != NULL) {
      $selected = 'linked_article';
    }
    switch ($selected) {
      case 'dot':
        $form_state->setResponse(new RedirectResponse('/node/add/dot_on_map'));
        break;
      case 'article':
        if (in_array('organization', \Drupal::currentUser()->getRoles())) {
          $form_state->setResponse(new RedirectResponse('/node/add/article'));
        }
        else {
          $form_state->setResponse(new RedirectResponse('/node/add/article?type=Aktiviteter'));
        }
        break;
      case 'education':
        $form_state->setResponse(new RedirectResponse('/node/add/article?type=Undervisningsforløb'));
        break;
      case 'linked_article':
        $form_state->setResponse(new RedirectResponse('/node/add/link_article?type=Undervisningsforløb'));
        break;
    }
  }

}
