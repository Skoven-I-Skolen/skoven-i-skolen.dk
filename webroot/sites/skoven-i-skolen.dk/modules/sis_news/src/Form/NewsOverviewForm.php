<?php

namespace Drupal\sis_news\Form;

use Drupal\entity_overview\Form\OverviewFilterForm;

class NewsOverviewForm extends OverviewFilterForm {

  public function getFormId() {
    return 'news_overview_form';
  }

}
