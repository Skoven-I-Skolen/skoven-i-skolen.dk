<?php

namespace Drupal\sis_articles\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\node\Entity\Node;
use Drupal\sis_articles\Services\ArticleContentDeliveryService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'article_filter_form' formatter.
 *
 * @FieldFormatter(
 *   id = "article_random_content",
 *   label = @Translation("Article random content"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class RandomContent extends EntityReferenceFormatterBase {

  /**
   * @var \Drupal\sis_articles\Services\ArticleContentDeliveryService
   */
  private ArticleContentDeliveryService $articleContentDeliveryService;

  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ArticleContentDeliveryService $articleContentDeliveryService) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->articleContentDeliveryService = $articleContentDeliveryService;
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      if ($entity->id()) {
        $articleId = $this->articleContentDeliveryService
          ->getArticleRepository()
          ->fetchRandomArticlesIdsByTaxonomy([
            'field_article_type' => $entity->id(),
          ], 4);

        if ($nodes = Node::loadMultiple($articleId)) {
          $first = array_shift($nodes);
          $elements[] = \Drupal::entityTypeManager()
            ->getViewBuilder('node')
            ->view($first, 'list');

          $links = [
            '#theme' => 'list_element',
            '#article_type' => $entity,
            '#list_headline' => '',
            '#links' => \Drupal::entityTypeManager()
              ->getViewBuilder('node')
              ->viewMultiple($nodes, 'link_list')
          ];

          $elements[] = $links;
        }
      }
    }

    return $elements;
  }


  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('sis_articles.content_delivery_service')
    );
  }

}
