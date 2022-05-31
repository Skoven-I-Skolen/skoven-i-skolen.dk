<?php

namespace Drupal\sis_lexicon\Services;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Link;
use Drupal\node\Entity\Node;
use Drupal\sis_lexicon\Repository\LexiconRepository;

class LexiconContentDeliveryService {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  private EntityTypeManager $entityTypeManager;

  /**
   * @var \Drupal\sis_lexicon\Repository\LexiconRepository
   */
  private LexiconRepository $lexiconRepository;

  public function __construct(LexiconRepository $lexiconRepository, EntityTypeManager $entityTypeManager) {
    $this->lexiconRepository = $lexiconRepository;
    $this->entityTypeManager = $entityTypeManager;
  }

  public function getFilters(int $limit = 0, int $page = 0, $options = []) {
    $alphapet = array_merge(range('A', 'Z'), ['Æ', 'Ø', 'Å']);
    $alphapet_links = [];

    $params = array_merge_recursive($options, [
      'attributes' => ['class' => ['use-ajax']],
      'query' => ['limit' => $limit, 'page' => $page]
    ]);

    foreach ($alphapet as $letter) {
      $params['query']['letter'] = $letter;
      $alphapet_links[] = Link::createFromRoute($letter, 'sis_lexicon.get_articles', [],
        $params
      );
    }

    return [
      '#theme' => 'lexicon_filters',
      '#items' => $alphapet_links,
    ];
  }

  /**
   * Get lexicon articles
   *
   * @param string $initialLetter
   *   Letter to show articles from.
   *
   * @return array|null
   *   Array of nodes matching the criteries.
   */
  public function getArticles(string $initialLetter, $limit = 2, $page = 0): ?array {
    if (!$lexiconArticlesIds = $this->lexiconRepository->getEntityIdsByInitialLetter($initialLetter, $limit, $page)) {
      return NULL;
    }

    $numberOfArticles = $this->lexiconRepository->getTotalNumberOfArticlesByLetter($initialLetter);

    $nodes = Node::loadMultiple($lexiconArticlesIds);
    $lexiconArticles = $this->entityTypeManager->getViewBuilder('node')
      ->viewMultiple($nodes, 'list');

    return [
      '#theme' => 'lexicon',
      '#articles' => $lexiconArticles,
      '#letter' => $initialLetter,
    ];
  }

  /**
   * Get lexicon articles
   *
   * @param string $initialLetter
   *   Letter to show articles from.
   *
   * @return array|null
   *   Array of nodes matching the criteries.
   */
  public function getArticlesByKeyword(string $keyword, $limit = 0, $page = 0): ?array {
    if (!$lexiconArticlesIds = $this->lexiconRepository->getEntityIdsByKeyword($keyword)) {
      return NULL;
    }

    $nodes = Node::loadMultiple($lexiconArticlesIds);
    $lexiconArticles = $this->entityTypeManager->getViewBuilder('node')
      ->viewMultiple($nodes, 'list');

    return [
      '#theme' => 'lexicon_search',
      '#articles' => $lexiconArticles,
    ];
  }

}
