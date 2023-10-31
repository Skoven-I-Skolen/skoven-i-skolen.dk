<?php

namespace Drupal\sis_overview\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Link;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

class TermBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  private ?Request $request;

  /**
   * @var \Drupal\Core\Routing\AdminContext
   */
  private AdminContext $adminContext;

  public function __construct(AdminContext $adminContext, \Symfony\Component\HttpFoundation\RequestStack $requestStack) {
    $this->request = $requestStack->getCurrentRequest();
    $this->adminContext = $adminContext;
  }


  public function applies(RouteMatchInterface $route_match) {

    if ($this->adminContext->isAdminRoute($route_match->getRouteObject())) {
      return FALSE;
    }

    if ($this->request->get('taxonomy_term')) {
      return TRUE;
    }

    return FALSE;
  }

  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addCacheContexts(['route']);

    $term = $this->request->get('taxonomy_term');

    $links = [
      Link::createFromRoute(t('Frontpage'), '<front>'),
      Link::createFromRoute($term->label(), '<none>'),
    ];

    $breadcrumb->setLinks($links);

    return $breadcrumb;
  }

}
