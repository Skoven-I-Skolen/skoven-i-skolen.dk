<?php

namespace Drupal\sis_almanac\Services;

use DateTime;
use Drupal\Component\Datetime\Time;

class AlmanacService {

  /**
   * @var \Drupal\Component\Datetime\Time
   */
  private Time $time;

  public function __construct(Time $time) {
    $this->time = $time;
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
  public function createDate($month, $day) {
    // We only have the day and month. So we take the year from the current request
    $year = date('Y', $this->time->getCurrentTime());

    // Build date string and create a nea DateTime object
    $dateString = $year . '-' . $month . '-' . $day;
    return DateTime::createFromFormat('Y-m-d', $dateString);
  }

  public function createdFormattedDate(int $month, int $day) {
    return $this->createDate($month, $day)->format('j F. Y');
  }

  /**
   * Check if date is today
   *
   * @param \DateTime $matchDate
   *   The data to check if its today
   * @return bool
   *   return TRUE if the date provided is today
   */
  public function isToday(int $month, int $day): bool {
    $matchDate = $this->createDate($month, $day);
    $matchDate->setTime( 0, 0, 0 );

    $today = new DateTime('today');
    $diff = $today->diff($matchDate);
    $diffDays = (integer)$diff->format( "%R%a" );
    return (bool) $diffDays == 0;
  }
}
