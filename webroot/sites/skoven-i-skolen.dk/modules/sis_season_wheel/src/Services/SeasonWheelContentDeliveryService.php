<?php

namespace Drupal\sis_season_wheel\Services;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Drupal\sis_articles\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeasonWheelContentDeliveryService {

  /**
   * @var \Drupal\sis_articles\Repository\ArticleRepository
   */
  private ArticleRepository $articleRepository;

  public function __construct(ArticleRepository $articleRepository, EntityTypeManager $entityTypeManager) {
    $this->articleRepository = $articleRepository;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Get articles by month term id .
   *
   * @param int $monthTermId
   *   The term Id matching the months.
   * @return array|null
   *   Render array containing the fetched articles.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getArticleByMonthTermId(int $monthTermId, $limit = 0): ?array {
    $fields = ['field_article_months' => $monthTermId];
    if ($articleIds = $this->articleRepository->fetchArticlesIdsByTaxonomy($fields, $limit)) {
      $entities = Node::loadMultiple($articleIds);

      return $this->entityTypeManager
        ->getViewBuilder('node')
        ->viewMultiple($entities ,'list');
    }

    return [];
  }

  /**
   * Get articles by month term id .
   *
   * @param int $monthTermId
   *   The term Id matching the months.
   * @return array|null
   *   Render array containing the fetched articles.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getRandomArticleByMonthTermId(int $monthTermId, $limit = 0): ?array {
    $fields = ['field_article_months' => $monthTermId];
    if ($articleIds = $this->articleRepository->fetchRandomArticlesIdsByTaxonomy($fields, $limit)) {
      return Node::loadMultiple($articleIds);
    }

    return [];
  }

}
