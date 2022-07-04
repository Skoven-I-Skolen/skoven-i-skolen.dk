<?php

namespace Drupal\premium_theme_helper\Breadcrumb;

use Drupal\content_hierarchy_breadcrumb\Breadcrumb\BreadcrumbBuilder as HierachyBreadcrumbBuilder;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\entity_hierarchy_breadcrumb\HierarchyBasedBreadcrumbBuilder;

class BreadcrumbBuilder extends HierachyBreadcrumbBuilder {

  public function build(RouteMatchInterface $route_match): Breadcrumb {
    $breadcrumb = new Breadcrumb();
    $hierachyBreadcrumb =  parent::build($route_match);
    $links = $hierachyBreadcrumb->getLinks();

    $links = $this->truncateBreadcrumb($links);
    $breadcrumb->setLinks($links);
    return $breadcrumb;
  }

  /**
   * Truncate breadcrumb.
   *
   * @param array $links
   *   Array of links to truncate.
   *
   * @return array|bool
   *  Array of truncated links.
   */
  protected function truncateBreadcrumb(array $links): array {
    if (count($links) > 3) {
      $links = array_slice($links, -2);
      $links = array_merge([
        Link::createFromRoute(t('Frontpage'), '<front>'),
        Link::createFromRoute('...', '<none>')
      ], $links);
    }
    return $links;
  }
}
