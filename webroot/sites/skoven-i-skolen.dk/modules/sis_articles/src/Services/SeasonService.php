<?php

namespace Drupal\sis_articles\Services;

use Drupal\Core\Datetime\DrupalDateTime;

class SeasonService {

  const WINTER = 1;

  const SPRING = 2;

  const SUMMER = 3;

  const AUTUMN = 4;

  private $now;

  public function __construct() {
    $this->now = new DrupalDateTime();
  }

  /**
   * Get the season
   *
   * @return int
   */
  public function getCurrentSeason() {
    $month = $this->now->format('m');
    return $this->calculateSeasons($month);
  }

  /**
   * Get current season term id.
   *
   * The term id's is set on the Article Configuration page.
   *
   * @return ?int
   *   The Term ID for the season.
   */
  public function getCurrentSeasonTermId(): ?int {
    return $this->getSeasonTaxonomyMapping($this->getCurrentSeason());
  }

  /**
   * Get the season ID by month number.
   *
   * @param int $month
   *   The months number.
   *
   * @return int
   *   The season Id.
   */
  public function getSeasonByMonth(int $month) {
    return $this->calculateSeasons($month);
  }

  /**
   * @param string $month
   *  The month number eg. 8 for august.
   *
   * @return int
   *  - 1 = winter
   *  - 2 = spring
   *  - 3 = summer
   *  - 4 = autumn
   */
  private function calculateSeasons(string $month): int {
    $month = intval($month);
    $seasons = [
      self::WINTER,
      self::SPRING,
      self::SUMMER,
      self::AUTUMN,
      self::WINTER,
    ];
    return $seasons[$month / 3];
  }

  /**
   * The the Term ID the macthes the season ID.
   *
   * The term id's is set on the Article Configuration page.
   *
   * @param int $seasonId
   *   The season ID the get the mapping for
   *
   * @return ?int
   *   The Term Id.
   */
  private function getSeasonTaxonomyMapping(int $seasonId): ?int {
    $config = \Drupal::config('sis_articles.settings');

    switch ($seasonId) {
      case self::WINTER:
        return $config->get('season_winter');
      case self::SPRING:
        return $config->get('season_spring');
      case self::SUMMER:
        return $config->get('season_summer');
      case self::AUTUMN:
        return $config->get('season_autumn');
    }

    return NULL;
  }

}
