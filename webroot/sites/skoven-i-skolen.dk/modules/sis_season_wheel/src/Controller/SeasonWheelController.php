<?php

namespace Drupal\sis_season_wheel\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Link;
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
  public function get(string $month): AjaxResponse {
    $monthTermId = $this->getMonthsIdByMachineName($month);
    $articles = $this->seasonWheelContentDeliveryService
      ->getArticleByMonthTermId($monthTermId, 8);

    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('.months-activity-cards', $articles));
    $response->addCommand(new HtmlCommand('#month_name', t(ucfirst($month))));

    // Create see all activitites link
    $link = Link::createFromRoute(
      t('See all activities'),
      'entity.taxonomy_term.canonical',
      ['taxonomy_term' => $monthTermId],
      ['attributes' => [
        'class' => 'seasonal-wheel__link js-seasonal-wheel__link'
      ]]
    )->toString();
    $response->addCommand(new ReplaceCommand('.js-seasonal-wheel__link', $link));

    return $response;
  }

  /**
   * @param string $machineName
   *
   * @return int
   */
  public function getMonthsIdByMachineName(string $machineName): int {
    $months = &drupal_static(__FUNCTION__, []);
    if (!empty($months[$machineName])) {
      return $months[$machineName];
    }

    /** @var \Drupal\sis_season_wheel\Repository\SeasonWheelRepository $repository */
    $repository = \Drupal::service('sis_season_wheel.repository');
    $months = $repository->getMonthsTaxonomyKeyedByMachineName();
    return $months[$machineName];
  }
}
