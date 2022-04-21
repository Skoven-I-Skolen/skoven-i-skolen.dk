<?php

namespace Drupal\sis_overview\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase {

  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('entity.taxonomy_term.canonical')) {
      $route->setDefault('_controller', '\Drupal\sis_overview\Controller\TermOverviewController::view');
    }
  }

}
