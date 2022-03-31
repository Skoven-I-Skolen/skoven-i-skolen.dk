<?php

namespace Drupal\sis_almanac\Controller;

use DateTime;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Renderer;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\sis_almanac\Repository\AlmanacRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AlmanacController extends ControllerBase {

  private Time $time;

  private AlmanacRepository $almanacRepository;

  /**
   * @var \Drupal\Core\Render\Renderer
   */
  private Renderer $renderer;

  public function __construct(AlmanacRepository $almanacRepository, Time $time, Renderer $renderer) {
    $this->time = $time;
    $this->almanacRepository = $almanacRepository;
    $this->renderer = $renderer;
  }

  /**
   * @inerhitDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sis_almanac.repository'),
      $container->get('datetime.time'),
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
    $date = $this->createDate($month, $day);

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

    return new JsonResponse(['data' => $output, 'status' => $statusCode ?? 200]);
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
    $date = $this->createDate($month, $day);

    $almanacToRender = [
      '#theme' => 'almanac',
      '#date' => $date->format('j. F Y'),
      '#content' => strip_tags($almanac->get('field_almanac_content')->value, '<a>'),
    ];
    return $this->renderer->render($almanacToRender);
  }

  /**
   * Create a DateTime object from day and month
   *
   * @param $month
   *   Numeric representation of a month
   * @param $day
   *   Day of the month without leading zeros
   *
   *
   * @return \DateTime|false
   */
  private function createDate($month, $day) {
    // We only have the day and month. So we take the year from the current request
    $year = date('Y', $this->time->getCurrentTime());

    // Build date string and create a nea DateTime object
    $dateString = $year . '-' . $month . '-' . $day;
    return DateTime::createFromFormat('Y-m-d', $dateString);
  }

}
