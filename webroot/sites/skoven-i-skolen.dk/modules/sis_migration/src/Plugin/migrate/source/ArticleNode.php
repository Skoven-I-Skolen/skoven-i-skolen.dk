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
