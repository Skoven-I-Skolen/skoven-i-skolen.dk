<?php

namespace Drupal\sis_almanac\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Renderer;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\sis_almanac\Repository\AlmanacRepository;
use Drupal\sis_almanac\Services\AlmanacService;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AlmanacController extends ControllerBase {

  private AlmanacRepository $almanacRepository;

  /**
   * @var \Drupal\Core\Render\Renderer
   */
  private Renderer $renderer;

  /**
   * @var \Drupal\sis_almanac\Services\AlmanacService
   */
  private AlmanacService $almanacService;

  public function __construct(AlmanacRepository $almanacRepository, AlmanacService $almanacService, Renderer $renderer) {
    $this->almanacRepository = $almanacRepository;
    $this->renderer = $renderer;
    $this->almanacService = $almanacService;
  }

  /**
   * @inerhitDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sis_almanac.repository'),
      $container->get('sis_almanac.service'),
      $container->get('renderer')
    );
  }

  /**
   * @param string $action
   * @param int $day
   * @param int $month
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse|void
   */
  public function get(string $action, int $day, int $month) {

    if (!is_numeric($day) || !is_numeric($month)) {
      return;
    }

    // Create a date from day and month for modification
    $date = $this->almanacService->createDate($month, $day);

    switch ($action) {
      case 'before':
        $day = $date->modify('-1 day')->format('j');
        $month = $date->modify('-1 day')->format('n');
        break;
      case 'after':
        $day = $date->modify('+1 day')->format('j');
        $month = $date->modify('+1 day')->format('n');
        break;
    }

    try {
      $output = [];
      if ($result = $this->almanacRepository->getAlmanacFromDayAndMonth($day, $month)) {
        $almanacs = Node::loadMultiple($result);

        foreach ($almanacs as $almanac) {
          $output[] = $this->buildAlmanac($almanac);
        }
      }
    } catch (\Exception $e) {
      $output = $e->getMessage();
      $statusCode = $e->getCode();
    }

    return new JsonResponse([
      'data' => $output,
      'status' => $statusCode ?? 200,
    ]);
  }

  /**
   * @param $almanac
   *   The almanac entity
   *
   * @return array
   *   Render array representing th almanac
   */
  private function buildAlmanac(NodeInterface $almanac): string {
    $month = $almanac->get('field_almanac_month')->value;
    $day = $almanac->get('field_almanac_day')->value;

    $almanacToRender = [
      '#theme' => 'almanac',
      '#date' => $this->almanacService->createdFormattedDate($month, $day),
      '#content' => [
        '#type' => 'processed_text',
        '#text' => $almanac->get('field_almanac_content')->value,
        '#format' => $almanac->get('field_almanac_content')->format,
      ],
    ];
    return $this->renderer->render($almanacToRender);
  }

}
