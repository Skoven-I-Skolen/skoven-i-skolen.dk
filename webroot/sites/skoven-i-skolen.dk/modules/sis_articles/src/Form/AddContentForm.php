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
      0 => '<strong>' . atom_str('share-page-first-option-title') . '</strong><div class="descriotion">' . atom_str('share-page-first-option-help-text') . '</div>',
      1 => '<strong>' . atom_str('share-page-second-option-title') . '</strong><div class="descriotion">' . atom_str('share-page-second-option-help-text') . '</div>',
      2 => '<strong>' . atom_str('share-page-third-option-title') . '</strong><div class="descriotion">' . atom_str('share-page-third-option-help-text') . '</div>',
    ];

    if (in_array('organization', \Drupal::currentUser()->getRoles())) {
      $options = [
        0 => '<strong>' . atom_str('share-page-first-option-title') . '</strong><div class="descriotion">' . atom_str('share-page-first-option-help-text') . '</div>',
        1 => '<strong>' . $this->t('Vil du oprette en artikel?') . '</strong><div class="descriotion">' . t('(En aktivitet, et undervisningsforløb, eller en anden artikeltype, der vises i interne søgeresultater og på din visitkortside)') . '</div>',
      ];
    }

    $form['content_type'] = [
      '#type' => 'radios',
      '#title' => $this
        ->t('Hvad vil du oprette?'),
      '#default_value' => 1,
      '#options' => $options,
    ];

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Continue'),
    );

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    switch ($form_state->getValue('content_type')) {
      case 0:
        $form_state->setResponse(new RedirectResponse('/node/add/dot_on_map'));
        break;
      case 1:
        if (in_array('organization', \Drupal::currentUser()->getRoles())) {
          $form_state->setResponse(new RedirectResponse('/node/add/article'));
        }
        else {
          $form_state->setResponse(new RedirectResponse('/node/add/article?type=Aktiviteter'));
        }
        break;
      case 2:
        $form_state->setResponse(new RedirectResponse('/node/add/article?type=Undervisningsforløb'));
        break;
    }
  }

}
