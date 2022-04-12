<?php

namespace Drupal\sis_almanac\Services;

use Drupal\node\Entity\Node;
use Drupal\sis_almanac\Repository\AlmanacRepository;

class AlmanacContentDeliveryService {

  /**
   * @var \Drupal\sis_almanac\Repository\AlmanacRepository
   */
  private AlmanacRepository $almanacRepository;

  /**
   * @var \Drupal\sis_almanac\Services\AlmanacService
   */
  private AlmanacService $almanacService;

  public function __construct(AlmanacRepository $almanacRepository, AlmanacService $almanacService) {
    $this->almanacRepository = $almanacRepository;
    $this->almanacService = $almanacService;
  }

  public function getAlmanac() {
    $almanacIds = $this->almanacRepository->getCurrentAlmanacWithPreviousAndNext();

    $almanacs = [];
    if (!empty($almanacIds)) {
      $almanacEntities = Node::loadMultiple($almanacIds);

      foreach ($almanacEntities as $almanac) {
        $month = $almanac->get('field_almanac_month')->value;
        $day = $almanac->get('field_almanac_day')->value;

        $almanacs[] = [
          '#theme' => 'almanac',
          '#date' => $this->almanacService->createdFormattedDate($month, $day),
          '#day' => $day,
          '#month' => $month,
          '#active' => $this->almanacService->isToday($month, $day),
          '#content' => [
            '#type' => 'processed_text',
            '#text' => $almanac->get('field_almanac_content')->value,
            '#format' => $almanac->get('field_almanac_content')->format,
          ],
        ];
      }
    }

    return [
      '#theme' => 'almanacs',
      '#almanacs' => $almanacs,
    ];
  }

}
