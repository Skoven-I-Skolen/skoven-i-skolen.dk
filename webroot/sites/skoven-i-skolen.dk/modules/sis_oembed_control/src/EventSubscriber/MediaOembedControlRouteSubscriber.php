<?php

namespace Drupal\sis_oembed_control\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class MediaOembedControlRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('media.oembed_iframe')) {
      $route->setDefault('_controller', '\Drupal\sis_oembed_control\Controller\OEmbedIframeController::render');
    }
  }

}
