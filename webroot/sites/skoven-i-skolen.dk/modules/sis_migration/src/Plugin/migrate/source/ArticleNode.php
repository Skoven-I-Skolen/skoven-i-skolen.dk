<?php

namespace Drupal\sis_migration\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d7\Node;

/**
 * @MigrateSource(
 *   id = "sis_article_node",
 * )
 */
class ArticleNode extends Node {

  public function prepareRow(Row $row) {
    parent::prepareRow($row);

    $subject_terms = $row->getSourceProperty('field_taxonomy_class');
    $subjects = [];
    if (!empty($subject_terms)) {
      $subjects = $this->select('taxonomy_term_data', 'ttd')
        ->fields('ttd', ['name'])
        ->condition('tid', array_column($subject_terms, 'tid', 'IN'))
        ->execute()->fetchCol();
    }

    $row->setSourceProperty('subjects', $subjects);

    $class_terms = $row->getSourceProperty('field_taxonomy_grade');
    $class = [];
    if (!empty($class_terms)) {
      $class = $this->select('taxonomy_term_data', 'ttd')
        ->fields('ttd', ['name'])
        ->condition('tid', array_column($class_terms, 'tid', 'IN'))
        ->execute()->fetchCol();
    }

    $row->setSourceProperty('class', $class);

    $location_terms = $row->getSourceProperty('field_location');
    $location = [];
    if (!empty($location_terms)) {
      $location = $this->select('taxonomy_term_data', 'ttd')
        ->fields('ttd', ['name'])
        ->condition('tid', array_column($location_terms, 'tid', 'IN'))
        ->execute()->fetchCol();
    }

    $row->setSourceProperty('location', $location);

    $season_terms = $row->getSourceProperty('field_taxonomy_season');
    $seasons = [];
    if (!empty($season_terms)) {
      $seasons = $this->select('taxonomy_term_data', 'ttd')
        ->fields('ttd', ['name'])
        ->condition('tid', array_column($season_terms, 'tid', 'IN'))
        ->execute()->fetchCol();
    }

    $row->setSourceProperty('season', $seasons);

    $category_term = $row->getSourceProperty('field_taxonomy_category');
    if (!empty($category_term)) {
      $parentId = array_column($category_term, 'tid', 'IN');
      while ($parentId != 207) {
        $query = $this->select('taxonomy_term_data', 'ttd')
          ->fields('tth', ['tid', 'parent']);

        $query->leftJoin('taxonomy_term_hierarchy', 'tth', 'tth.parent = ttd.tid');
        $result = $query->condition('tth.tid', $parentId)
          ->execute()
          ->fetchAllKeyed();

        $parentId = reset($result);
      }

      $row->setSourceProperty('article_type', array_key_first($result));
    }

    return TRUE;
  }

}
