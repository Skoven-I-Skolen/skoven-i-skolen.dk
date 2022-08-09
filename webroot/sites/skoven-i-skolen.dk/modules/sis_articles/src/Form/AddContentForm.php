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

    $form['content_type'] = [
      '#type' => 'radios',
      '#title' => $this
        ->t('Hvad vil du oprette?'),
      '#default_value' => 1,
      '#options' => [
        0 => $this->t('Vil du have en prik på kortet?'),
        1 => $this->t('Vil du lægge en aktivitet på kortet'),
        2 => $this->t('Vil du lægge et undervisningsforløb på kortet'),
      ],
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
        $form_state->setResponse(new RedirectResponse('/node/add/article?type=Aktivitet'));
        break;
      case 2:
        $form_state->setResponse(new RedirectResponse('/node/add/article?type=Undervisningsforløb'));
        break;
    }
  }

}
