<?php

namespace Drupal\sis_lexicon\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Http\RequestStack;
use Drupal\sis_lexicon\Services\LexiconContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LexiconController extends ControllerBase {

  /**
   * @var \Drupal\sis_lexicon\Services\LexiconContentDeliveryService
   */
  private LexiconContentDeliveryService $lexiconContentDelivery;

  private ?\Symfony\Component\HttpFoundation\Request $request;

  public function __construct(LexiconContentDeliveryService $lexiconContentDelivery, RequestStack $requestStack) {
    $this->lexiconContentDelivery = $lexiconContentDelivery;
    $this->request = $requestStack->getCurrentRequest();
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sis_lexicon.content_delivery'),
      $container->get('request_stack')
    );
  }

  /**
   * @param string $letter
   *  The initial letter to find lexicon articles for
   *
   * @return void
   */
  public function get(): ?AjaxResponse {

    $response = new AjaxResponse();

    if(!$content = $this->lexiconContentDelivery->getArticles($this->getLetter(), $this->getLimit(), $this->getPage())) {
      $content = [
        '#theme' => 'lexicon',
        '#articles' => atom_str('lexicon-no-result')
      ];
    }

    if (is_array($content['#articles']) && $this->getPager()) {
      $content['#pager'] = TRUE;
    }

    $response->addCommand(new ReplaceCommand('#lexicon-inner', $content));

    return $response;
  }

  /**
   * @param string $letter
   *  The initial letter to find lexicon articles for
   *
   * @return void
   */
  public function search(): ?AjaxResponse {

    $keyword = $this->getKeyword();
    $limit = $this->getLimit();
    $page = $this->getPage();

    $response = new AjaxResponse();

    if (empty($keyword)) {
      return $response;
    }

    if(!$content = $this->lexiconContentDelivery->getArticlesByKeyword($keyword, $limit, $page)) {
      $content = [
        '#theme' => 'lexicon_search',
        '#articles' => 'No results found'
      ];
    }

    $response->addCommand(new ReplaceCommand('#lexicon-items', $content));

    return $response;
  }

  /**
   * Get letter query parameter from request.
   *
   * @return string
   *   The letter
   */
  public function getLetter() {
    return $this->request->get('letter', 'A');
  }

  /**
   * Get limit query parameter from request.
   *
   * @return int
   *   The limit
   */
  public function getLimit() {
    return $this->request->get('limit', 2);
  }

  /**
   * Get page query parameter from request.
   *
   * @return int
   *   The page
   */
  public function getPage() {
    return $this->request->get('page', 1);
  }

  /**
   * Get pager query parameter from request.
   *
   * @return bool
   *   The page
   */
  public function getPager() {
    return (bool) $this->request->get('pager', false);
  }

  /**
   * Get keyword query parameter from request.
   *
   * @return string
   *   The keyword
   */
  public function getKeyword() {
    return $this->request->get('keyword', null);
  }
}
