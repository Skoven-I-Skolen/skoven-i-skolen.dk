<?php

namespace Drupal\sis_news\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_overview\Plugin\Field\FieldFormatter\OverviewFormFormatter;
use Drupal\layout_builder\Plugin\Block\InlineBlock;
use Drupal\sis_news\Form\NewsOverviewForm;
use Drupal\taxonomy\Entity\Term;

/**
 * Plugin implementation of the 'article_filter_form' formatter.
 *
 * @FieldFormatter(
 *   id = "news_overview_formatter",
 *   label = @Translation("News overview"),
 *   field_types = {
 *     "article_filter",
 *     "overview_filter"
 *   }
 * )
 */
class NewsOverviewFormatter extends OverviewFormFormatter {

}
