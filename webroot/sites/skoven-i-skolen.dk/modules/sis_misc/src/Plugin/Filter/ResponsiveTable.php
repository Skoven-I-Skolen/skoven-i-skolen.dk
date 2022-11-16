<?php

namespace Drupal\sis_misc\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Provides a filter to wrap each table into DIV with class name "table-responsive".
 *
 * @Filter(
 *   id = "filter_responsive_table",
 *   title = @Translation("Responsive table"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class ResponsiveTable extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $regex = '/<table[^>]*>.*?<\/table>/si';
    $text = preg_replace_callback($regex, [$this, "replace"], $text);
    return new FilterProcessResult($text);
  }

  public function replace($match) {
    return "<div class=\"table-responsive\">" . $match[0] . "</div>";
  }

}
