<?php

namespace Drupal\sis_relewise\Controller;

use Drupal\relewise\Controller\RelewiseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AutocompleteController extends RelewiseController {

  /**
   * Return data for use in autocomplete
   * @param $term
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function autocomplete($term): JsonResponse {
    $result = $this->relewise->searchContent($term, [], NULL, 10, 0, ['SelectedContentProperties' => ['AllData' => TRUE]]);
    $test = '';
  }
}
