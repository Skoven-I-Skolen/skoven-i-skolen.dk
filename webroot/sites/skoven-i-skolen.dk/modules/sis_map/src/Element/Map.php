<?php

namespace Drupal\sis_map\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Defines a render element for builder a map.
 *
 * @RenderElement("map")
 *
 * @internal
 *   Plugin classes are internal
 */
class Map extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'map',
    ];
  }

}
