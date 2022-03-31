<?php

namespace Drupal\sis_almanac\Repository;

use Drupal\node\NodeInterface;

class AlmanacRepository {

  /**
   * @param $month
   *   Numeric representation of a month
   * @param $day
   *   Day of the month without leading zeros
   *
   * @return int[]
   *   Array ot node ids
   */
  public function getAlmanacFromDayAndMonth(int $day, int $month): array {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'almanac')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('field_almanac_day', $day, '=')
      ->condition('field_almanac_month', $month, '=');

    return $query->execute();
  }

}
