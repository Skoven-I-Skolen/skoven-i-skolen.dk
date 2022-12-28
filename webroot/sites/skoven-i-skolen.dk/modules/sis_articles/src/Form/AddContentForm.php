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
      0 => '<strong>' . $this->t('Vil du have en prik på kortet?') . '</strong><div class="descriotion">' . t('(Institutioner som kan hjælpe udeskole lokalt, f.eks. udeskole, naturvejleder, jæger eller andet, ikke vist i de interne søgeresultater)') . '</div>',
      1 => '<strong>' . $this->t('Vil du lægge en aktivitet på kortet') . '</strong><div class="descriotion">' . t('(En aktivitet er en ide, som ikke nødvendigvis er knyttet til et fag og faglige mål)') . '</div>',
      2 => '<strong>' . $this->t('Vil du lægge et undervisningsforløb på kortet') . '</strong><div class="descriotion">' . t('(Et undervisningsforløb er knyttet til fag, klasse og faglige mål)') . '</div>',
    ];

    if (in_array('organization', \Drupal::currentUser()->getRoles())) {
      $options = [
        0 => '<strong>' . $this->t('Vil du have en prik på kortet?') . '</strong><div class="descriotion">' . t('(Institutioner som kan hjælpe udeskole lokalt, f.eks. udeskole, naturvejleder, jæger eller andet, ikke vist i de interne søgeresultater)') . '</div>',
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
