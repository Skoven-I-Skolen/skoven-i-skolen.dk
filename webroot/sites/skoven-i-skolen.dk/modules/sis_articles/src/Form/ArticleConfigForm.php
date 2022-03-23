<?php

namespace Drupal\sis_articles\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;

class ArticleConfigForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['sis_articles.settings'];
  }

  public function getFormId() {
    return 'sis_article_config_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sis_articles.settings');

    $form['seasons'] = [
      '#type' => 'details',
      '#title' => $this->t('Season config'),
    ];

    $season_config_names = [
      'season_winter' => $this->t('Winter'),
      'season_spring' => $this->t('Spring'),
      'season_summer' => $this->t('Summer'),
      'season_autumn' => $this->t('Autumn'),
    ];

    foreach ($season_config_names as $season_config_name => $title) {
      $default_value = $config->get($season_config_name);

      $form['seasons'][$season_config_name] = [
        '#title' => $title,
        '#type' => 'entity_autocomplete',
        '#target_type' => 'taxonomy_term',
        '#selection_handler' => 'default',
        // Optional. The default selection handler is pre-populated to 'default'.
        '#selection_settings' => [
          'target_bundles' => ['season'],
        ],
        '#default_value' => $default_value ? Term::load($default_value) : NULL,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('sis_articles.settings')
      // Remove unchecked types.
      ->set('season_winter', $form_state->getValue('season_winter'))
      ->set('season_spring', $form_state->getValue('season_spring'))
      ->set('season_summer', $form_state->getValue('season_summer'))
      ->set('season_autumn', $form_state->getValue('season_autumn'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
