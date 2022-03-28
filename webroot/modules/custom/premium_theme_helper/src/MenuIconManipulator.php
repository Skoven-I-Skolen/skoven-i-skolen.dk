<?php

namespace Drupal\premium_theme_helper;

/**
 * Menu icon manipulator.
 */
class MenuIconManipulator {

  /**
   * Add icon.
   *
   * @param array $tree
   *   Tree.
   */
  public function addIcon(array $tree): array {
    $icons = $this->loadIcons();

    foreach ($tree as $key => $value) {
      $plugin_definition = $value->link->getPluginDefinition();

      if (!empty($plugin_definition['metadata']['entity_id'])) {
        $entity_id = $plugin_definition['metadata']['entity_id'];
        $value->link->icon = $icons[$entity_id];
      }
    }
    return $tree;

  }

  /**
   * Load icons.
   */
  protected function loadIcons() {
    $db = \Drupal::database();
    return $db->select('menu_link_content_data', 'm')->fields('m', [
      'id',
      'icon__value',
    ])->execute()->fetchAllKeyed();
  }

}
