<?php

namespace Drupal\sis_almanac\Repository;

use DateTime;
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
      ->condition('field_almanac_day', $day)
      ->condition('field_almanac_month', $month);

    return $query->execute();
  }

  public function getCurrentAlmanacWithPreviousAndNext() {
    $date = new DateTime('yesterday');

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'almanac')
      ->condition('status', NodeInterface::PUBLISHED);

    $previous = $query->andConditionGroup();
    $previous
      ->condition('field_almanac_day', $date->format('j'))
      ->condition('field_almanac_month', $date->format('n'));

    $current = $query->andConditionGroup();
    $current
      ->condition('field_almanac_day', $date->modify('+ 1 day')->format('j'))
      ->condition('field_almanac_month', $date->format('n'));

    $next = $query->andConditionGroup();
    $next
      ->condition('field_almanac_day', $date->modify('+ 1 day')->format('j'))
      ->condition('field_almanac_month', $date->format('n'));

    $after2days = $query->andConditionGroup();
    $after2days
      ->condition('field_almanac_day', $date->modify('+ 1 day')->format('j'))
      ->condition('field_almanac_month', $date->format('n'));

    $after3days = $query->andConditionGroup();
    $after3days
      ->condition('field_almanac_day', $date->modify('+ 1 day')->format('j'))
      ->condition('field_almanac_month', $date->format('n'));

    $after4days = $query->andConditionGroup();
    $after4days
      ->condition('field_almanac_day', $date->modify('+ 1 day')->format('j'))
      ->condition('field_almanac_month', $date->format('n'));

    $after5days = $query->andConditionGroup();
    $after5days
      ->condition('field_almanac_day', $date->modify('+ 1 day')->format('j'))
      ->condition('field_almanac_month', $date->format('n'));

    $after6days = $query->andConditionGroup();
    $after6days
      ->condition('field_almanac_day', $date->modify('+ 1 day')->format('j'))
      ->condition('field_almanac_month', $date->format('n'));

    $days = $query->orConditionGroup();
    $days
      ->condition($previous)
      ->condition($current)
      ->condition($next)
      ->condition($after2days)
      ->condition($after3days)
      ->condition($after4days)
      ->condition($after5days)
      ->condition($after6days);

    $query->condition($days);
    $query->sort('field_almanac_day');
    $query->sort('field_almanac_month');

    return $query->execute();
  }
}
