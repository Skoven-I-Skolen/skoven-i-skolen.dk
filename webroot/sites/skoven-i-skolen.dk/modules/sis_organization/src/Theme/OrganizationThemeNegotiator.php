<?php

namespace Drupal\sis_organization\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

class OrganizationThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * @inheritDoc
   */
  public function applies(RouteMatchInterface $route_match) {
    if ($routeObject = $route_match->getRouteObject()) {
      $path = $routeObject->getPath();
      $currentUser = \Drupal::currentUser();

      if (($path === '/node/add/{node_type}' || $path === '/node/{node}/edit') && in_array('organization', $currentUser->getRoles(), TRUE)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * @inheritDoc
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return 'sis';
  }

}
