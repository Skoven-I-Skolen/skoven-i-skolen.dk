<?php

namespace Drupal\sis_lexicon\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\sis_lexicon\Services\LexiconContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LexiconController extends ControllerBase {

  /**
   * @var \Drupal\sis_lexicon\Services\LexiconContentDeliveryService
   */
  private LexiconContentDeliveryService $lexiconContentDelivery;

  public function __construct(LexiconContentDeliveryService $lexiconContentDelivery) {
    $this->lexiconContentDelivery = $lexiconContentDelivery;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sis_lexicon.content_delivery')
    );
  }

  /**
   * @param string $letter
   *  The initial letter to find lexicon articles for
   *
   * @return void
   */
  public function get(string $letter): ?AjaxResponse {

    if(!$content = $this->lexiconContentDelivery->getArticles($letter)) {
      return null;
    }

    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#lexicon-items', $content));

    return $response;
  }

}
