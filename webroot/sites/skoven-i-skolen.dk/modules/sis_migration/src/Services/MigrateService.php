<?php

namespace Drupal\sis_migration\Services;

class MigrateService {
  public static function extractAuthor(string $data): string {
    return self::extractFromPrefix('Forfatter', $data);
  }

  public static function extractEditor(string $data): string {
    return self::extractFromPrefix('Redaktør', $data);
  }

  public static function extractPhotographer(string $data): string {
    return self::extractFromPrefix('Foto', $data);
  }

  public static function getMonth(string $data): int {
  }

  /**
   * Extract string from prefix
   *
   * @param string $prefix
   *   The prefix-
   * @param string $data
   *  The data to search within-
   * @return mixed
   *  The extracted value.
   */
  protected static function extractFromPrefix(string $prefix, string $data) {
    preg_match('/'. $prefix .': ([^\n\r]*)/g', $data, $matches);
    return $matches[1];
  }
}
