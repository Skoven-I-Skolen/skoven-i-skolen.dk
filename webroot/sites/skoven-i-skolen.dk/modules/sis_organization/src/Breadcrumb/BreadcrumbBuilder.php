<?php

namespace Drupal\sis_organization\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Link;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

class BreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  private ?Request $request;

  /**
   * @var \Drupal\Core\Routing\AdminContext
   */
  private AdminContext $adminContext;

  public function __construct(AdminContext $adminContext, RequestStack $requestStack) {
    $this->request = $requestStack->getCurrentRequest();
    $this->adminContext = $adminContext;
  }


  public function applies(RouteMatchInterface $route_match) {

    if ($this->adminContext->isAdminRoute($route_match->getRouteObject())) {
      return FALSE;
    }

    if ($this->request->get('profile')) {
      return TRUE;
    }

    return FALSE;
  }

  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addCacheContexts(['route']);

    $profile = $this->request->get('profile');

    $links = [
      Link::createFromRoute(t('Frontpage'), '<front>'),
      Link::createFromRoute(t('Organizations'), 'sis_organization.overview'),
      Link::createFromRoute($profile->get('field_organization_address')->organization, '<none>'),
    ];

    $breadcrumb->setLinks($links);

    return $breadcrumb;
  }

}
