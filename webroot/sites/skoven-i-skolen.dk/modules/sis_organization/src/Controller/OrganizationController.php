<?php

namespace Drupal\sis_organization\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Pager\PagerManager;
use Drupal\sis_organization\Repository\OrganizationRepository;
use Drupal\sis_organization\Services\OrganizationContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class OrganizationController extends ControllerBase {

  private OrganizationContentDeliveryService $contentDelivery;

  /**
   * @var \Drupal\Core\Pager\PagerManager
   */
  private PagerManager $pagerManager;

  /**
   * @var \Drupal\sis_organization\Repository\OrganizationRepository
   */
  private OrganizationRepository $organizationRepository;

  /**
   * @var \Symfony\Component\HttpFoundation\Request|null
   */
  private ?Request $request;

  public function __construct(OrganizationContentDeliveryService $contentDelivery, OrganizationRepository $organizationRepository, PagerManager $pagerManager, RequestStack $requestStack) {
    $this->contentDelivery = $contentDelivery;
    $this->pagerManager = $pagerManager;
    $this->organizationRepository = $organizationRepository;
    $this->request = $requestStack->getCurrentRequest();
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sis_organization.content_delivery'),
      $container->get('sis_organization.organization_repository'),
      $container->get('pager.manager'),
      $container->get('request_stack')
    );
  }

  /**
   * Build organizations overview.
   * @return array
   */
  public function overview() {
    $this->pagerManager->createPager($this->organizationRepository->getNumberOfOrganizations(), 16)
      ->getCurrentPage();

    $build = [];
    $build['#theme'] = 'page__organizations';
    $build['title'] = ['#markup' => atom_str('organization-overview-headline')];
    $build['description'] = ['#markup' => atom_str('organization-overview-text')];
    $build['content'] = $this->contentDelivery->getOrganizations($this->getLimit(), $this->getPage());
    $build['pager'] = [
      '#type' => 'pager',
      '#route_name' => 'sis_organization.get',
    ];
    return $build;
  }

  /**
   * Show entities attached to taxonomy term, with limit.
   *
   * @param \Drupal\taxonomy\Entity\Term $taxonomy_term
   *   The taxonomy term.
   *
   * @return AjaxResponse
   *   AjaxResponse
   */
  public function get(): AjaxResponse {
    $build = $this->overview();

    $ajaxResponse = new AjaxResponse();
    $ajaxResponse->addCommand(new HtmlCommand('#organization_content', $build['content']))
      ->addCommand(new ReplaceCommand('.pager', $build['pager']));
    return $ajaxResponse;
  }

  /**
   * Get limit query parameter from request.
   *
   * @return int
   *   The limit
   */
  public function getLimit() {
    return $this->request->get('limit', 16);
  }

  /**
   * Get page query parameter from request.
   *
   * @return int
   *   The page
   */
  public function getPage() {
    return $this->request->get('page', 0);
  }


  public function title() {
    return atom_str('organization-overview-headline');
  }

}
