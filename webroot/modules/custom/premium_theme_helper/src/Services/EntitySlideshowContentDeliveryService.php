<?php

namespace Drupal\premium_theme_helper\Services;

use Drupal\block_content\Entity\BlockContent;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Render\Element;
use Drupal\node\Entity\Node;
use Drupal\premium_theme_helper\Repository\EntitySlideshowRepository;
use Drupal\sis_articles\Repository\ArticleRepository;
use Drupal\sis_articles\Services\ArticleContentDeliveryService;

class EntitySlideshowContentDeliveryService {

  private EntitySlideshowRepository $entitySlideShowRepository;


  /**
   * @var \Drupal\sis_articles\Services\ArticleContentDeliveryService
   */
  private ArticleContentDeliveryService $articleContentDelivery;

  private ArticleRepository $articleRepository;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  private EntityTypeManager $entityTypeManager;

  /**
   * @param \Drupal\premium_theme_helper\Repository\EntitySlideshowRepository $entitySlideShowRepository
   * @param \Drupal\sis_articles\Repository\ArticleRepository $articleRepository
   */
  public function __construct(EntitySlideshowRepository $entitySlideShowRepository, ArticleRepository $articleRepository, EntityTypeManager $entityTypeManager) {
    $this->entitySlideShowRepository = $entitySlideShowRepository;
    $this->articleRepository = $articleRepository;
    $this->entityTypeManager = $entityTypeManager;
  }


  /**
   * @param \Drupal\block_content\Entity\BlockContent $blockContent
   *
   * @return array
   */
  function getEntitiesForSlideshow(BlockContent $blockContent) {
    $fields = [];
    $entityIds = [];
    $bundle = $blockContent->get('field_entity_bundle')->target_id;

    // If not bundle has selected, its possible individual entities (nodes)
    if ($bundle === '_none' && !$blockContent->get('field_entity_slideshow_items')
        ->isEmpty()) {
      foreach ($blockContent->get('field_entity_slideshow_items') as $value) {
        $entityIds[] = $value->target_id;
      }
    }

    if (empty($entityIds)) {
      // If the article bundle is chosen, is so possible to select the article type
      if ($bundle === 'article') {
        $target_id = $blockContent->get('field_entity_slideshow_term')->target_id;

        if ($term = \Drupal::request()->get('taxonomy_term')) {
          $target_id = $term->id();
        }

        $fields = [
          'field_article_type' => $target_id,
        ];
      }

      $entityIds = $this->entitySlideShowRepository->getEntitiesByBundleAndTerm($bundle, $fields, 4,);
    }

    if ($entities = Node::loadMultiple($entityIds)) {
      return $this->entityTypeManager
        ->getViewBuilder('node')
        ->viewMultiple($entities, 'slideshow');
    }

    return [];

  }

}
