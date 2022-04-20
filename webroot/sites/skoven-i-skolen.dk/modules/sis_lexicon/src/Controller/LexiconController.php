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
  public function get(string $letter, $limit = 2, $page = 0): ?AjaxResponse {

    $response = new AjaxResponse();

    if(!$content = $this->lexiconContentDelivery->getArticles($letter, $limit, $page)) {
      $content = [
        '#theme' => 'lexicon',
        '#articles' => 'No results found'
      ];
    }

    $test = $this->request->get('pager');
    if ($this->request->get('pager') === '1') {
      $content['#pager'] = TRUE;
    }

    $response->addCommand(new ReplaceCommand('#lexicon-items', $content));

    return $response;
  }

  /**
   * @param string $letter
   *  The initial letter to find lexicon articles for
   *
   * @return void
   */
  public function search(): ?AjaxResponse {

    $keyword = $this->request->get('keyword') ?? null;
    $limit = $this->request->get('number') ?? 0;
    $page = $this->request->get('page') ?? 0;

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

}
