<?php

namespace Drupal\sis_lexicon\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_overview\Form\OverviewFilterForm;
use Drupal\entity_overview\OverviewFilter;
use Drupal\entity_overview\OverviewManager;
use Drupal\entity_overview\Services\OverviewFormStateService;
use Drupal\node\Entity\Node;
use Drupal\sis_lexicon\Services\LexiconContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LexiconOverviewForm extends OverviewFilterForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_overview_filter_lexicon_form';
  }


  public function buildForm(array $form, FormStateInterface $form_state, OverviewFilter $filter = NULL) {
    $form = parent::buildForm($form, $form_state, $filter);

    return $form;
  }

}
