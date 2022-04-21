<?php

namespace Drupal\sis_overview\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Pager\PagerManager;
use Drupal\sis_overview\Repository\TermRepository;
use Drupal\sis_overview\Service\TermContentDeliveryService;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class TermOverviewController extends ControllerBase {

  const DEFAULT_LIMIT = 16;

  private TermContentDeliveryService $termContentDelivery;

  private ?Request $request;

  /**
   * @var \Drupal\sis_overview\Repository\TermRepository
   */
  private TermRepository $termRepository;

  /**
   * @var \Drupal\Core\Pager\PagerManager
   */
  private PagerManager $pagerManager;

  public function __construct(TermContentDeliveryService $termContentDelivery, TermRepository $termRepository, RequestStack $requestStack, PagerManager $pagerManager) {
    $this->termContentDelivery = $termContentDelivery;
    $this->request = $requestStack->getCurrentRequest();
    $this->termRepository = $termRepository;
    $this->pagerManager = $pagerManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sis_overview.content_delivery'),
      $container->get('sis_overview.term_repository'),
      $container->get('request_stack'),
      $container->get('pager.manager'),
    );
  }

  /**
   * Show entities attached to taxonomy term
   *
   * @param \Drupal\taxonomy\Entity\Term $taxonomy_term
   *   The taxonomy term
   *
   * @return array
   *   Array of entites to render
   */
  public function view(Term $taxonomy_term): array {

//    if ($taxonomy_term->bundle() === 'article_types') {
//      return $taxonomy_term->get('field_overview')->view([
//        'label' => 'hidden',
//        'type' => 'taxonomy_overview_formatter'
//      ]);
//    }

    $this->pagerManager
      ->createPager($this->termRepository->getNumberOfItemsWithTaxonomyTerm($taxonomy_term->id()), $this->getLimit())
      ->getCurrentPage();

    $build['content'] = $this->termContentDelivery
      ->getEntitiesByTerm($taxonomy_term, $this->getLimit(), $this->getPage());

    $build['pager'] = [
      '#type' => 'pager',
      '#route_name' => 'sis_overview.term.get',
      '#route_parameters' => ['taxonomy_term' => $taxonomy_term->id()],
    ];
    return $build;
  }

  /**
   * Show entities attached to taxonomy term, with limit.
   *
   * @param \Drupal\taxonomy\Entity\Term $taxonomy_term
   *   The taxonomy term.
   * @return AjaxResponse
   *   AjaxResponse
   */
  public function get(Term $taxonomy_term): AjaxResponse {
    $build = $this->view($taxonomy_term);

    $ajaxResponse = new AjaxResponse();
    $ajaxResponse->addCommand(new HtmlCommand('#term_content', $build['content']))
      ->addCommand(new ReplaceCommand('.pager',$build['pager']));
    return $ajaxResponse;
  }

  /**
   * Get the limit parameter from request.
   *
   * @return int
   *   The limit.
   */
  private function getLimit() {
    return $this->request->get('limit', self::DEFAULT_LIMIT);
  }


  /**
   * Get the limit parameter from request.
   *
   * @return int
   *   The limit.
   */
  private function getPage() {
    return $this->request->get('page', 0);
  }

}
