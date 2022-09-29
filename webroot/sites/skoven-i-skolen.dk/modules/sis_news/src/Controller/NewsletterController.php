<?php

namespace Drupal\sis_news\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Http\RequestStack;
use Drupal\sis_lexicon\Services\LexiconContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends ControllerBase {

  private $url = 'https://np-skjoldundernes-land.uxmail.io/handlers/post/';
  private Request $request;

  public function __construct(RequestStack $requestStack) {
    $this->request = $requestStack->getCurrentRequest();
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  public function subscribe() {
    $client = \Drupal::httpClient();
    $secret = $this->request->get('secret');
    if ($secret) {
      return new RedirectResponse('/403');
    }
    $request = $client->post($this->url, [
      'optin_scheme' => $this->request->get('optin_scheme'),
      'action' => $this->request->get('action'),
      'lists' => $this->request->get('lists'),
      'data_Navn' => $this->request->get('data_Navn'),
      'email_address' => $this->request->get('email_address'),
    ]);

    if ($request->getStatusCode() === 200) {
      return new RedirectResponse('/newsletter/tak');
    }

    else {
      return new RedirectResponse('/403');
    }
  }

}
