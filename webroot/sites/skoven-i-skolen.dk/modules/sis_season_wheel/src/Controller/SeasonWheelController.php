<?php

namespace Drupal\sis_season_wheel\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\sis_articles\Repository\ArticleRepository;
use Drupal\sis_season_wheel\Services\SeasonWheelContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SeasonWheelController extends ControllerBase {
  /**
   * @var \Drupal\sis_season_wheel\Services\SeasonWheelContentDeliveryService
   */
  private SeasonWheelContentDeliveryService $seasonWheelContentDeliveryService;

  public function __construct(SeasonWheelContentDeliveryService $seasonWheelContentDeliveryService) {
    $this->seasonWheelContentDeliveryService = $seasonWheelContentDeliveryService;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sis_season_wheel.content_delivery_service'),
    );
  }

  /**
   * Get articles by month.
   *
   * @param int $month
   *   The term Id matching the months.
   * @return array
   *   Render array containing the fetched articles.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function get(int $month): AjaxResponse {
    $articles = $this->seasonWheelContentDeliveryService
      ->getArticleByMonthTermId($month, 8);

    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('.months-activity-cards', $articles));
    return $response;
  }

}
