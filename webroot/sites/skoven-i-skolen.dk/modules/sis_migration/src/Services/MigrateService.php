<?php

namespace Drupal\sis_migration\Services;

class MigrateService {

  public static function extractAuthor(array $data): ?string {
    return self::extractFromPrefix('Forfatter', $data['value']);
  }

  public static function extractEditor(array $data): ?string {
    return self::extractFromPrefix('Redaktør', $data['value']);
  }

  public static function extractPhotographer(array $data): ?string {
    return self::extractFromPrefix('Foto', $data['value']);
  }

  /**
   * Extract string from prefix
   *
   * @param string $prefix
   *   The prefix-
   * @param string $data
   *  The data to search within-
   *
   * @return mixed
   *  The extracted value.
   */
  protected static function extractFromPrefix(string $prefix, string $data): ?string {
    preg_match('/' . $prefix . ': ([^\n\r]*)/', $data, $matches);

    if (isset($matches[1])) {
      return strip_tags($matches[1]);
    }

    return NULL;
  }

  public static function mapCategoriesToArticleTypes(int $typeId) {
    $type = '';
    switch ($typeId) {
      case 215:
        $type = 'aktivitet';
        break;
      case 216:
        $type = 'undervisningsforloeb';
        break;
      case 217:
        $type = 'tema';
        break;
      case 220:
        $type = 'skovmad';
        break;
      case 221:
        $type = 'bog';
        break;
      case 223:
        $type = 'aarstidshjul';
        break;
      case 224:
        $type = 'lexicon';
        break;
      case 234:
        $type = 'viden_om_udeskole';
        break;
    }

    $article_type = \Drupal::entityQuery('taxonomy_term')
      ->condition('machine_name', $type)
      ->condition('vid', 'article_types')
      ->execute();

    if(!empty($article_type)) {
      return reset($article_type);
    }
  }

  public static function mapTaxonomyTerms($something) {
    $a = 0;
  }

}
