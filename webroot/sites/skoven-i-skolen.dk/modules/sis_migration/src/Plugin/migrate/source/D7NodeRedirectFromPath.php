<?php

namespace Drupal\sis_migration\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\path\Plugin\migrate\source\d7\UrlAlias;

/**
 * Drupal 7 node source from database.
 *
 * @MigrateSource(
 *   id = "sis_node_redirect_from_path",
 *   source_module = "node"
 * )
 */
class D7NodeRedirectFromPath extends UrlAlias {

  public function query() {
    // Get the database query from the UrlAlias class.
    $query = parent::query();

    // Add our condition to filter for only node paths.
    $query->condition('ua.source', 'node/%', 'LIKE');

    // Return the modified query.
    return $query;
  }

  public function prepareRow(Row $row) {
    // Get the source field from the row.
    $source = $row->getSourceProperty('source');

    // If it matches node/nodeID...
    if (preg_match('/node\/[0-9]+/', $source)) {
      // Get the node ID from the string.
      $nid = substr($source, 5);

      // Provide it to the migration as the "nid" field.
      $row->setSourceProperty('nid', $nid);
    }

    // return the result.
    return parent::prepareRow($row);
  }

}
