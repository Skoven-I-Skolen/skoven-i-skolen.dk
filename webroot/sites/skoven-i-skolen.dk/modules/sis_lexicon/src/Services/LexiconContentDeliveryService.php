<?php

namespace Drupal\sis_lexicon\Services;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Link;
use Drupal\Core\Url;
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
      $url = Url::fromRoute('sis_lexicon.get_articles', $params['query']);
      $alphapet_links[] = [
        '#type' => 'html_tag',
        '#tag' => 'button',
        '#value' => $letter,
        '#attributes' => [
          'href' => $url->toString(),
          'class' => 'use-ajax',
        ],
      ];
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

    // SIS2-693: This is a hacky solution for an issue where Å and å are
    // handled like A and a by MySQL. The other solution would be changing the
    // collation on the server, which is not easily doable for SiS.
    $is_single_a = ($initialLetter === 'A' || $initialLetter === 'a');
    $is_double_a = ($initialLetter === 'Å' || $initialLetter === 'å');
    if ($is_double_a || $is_single_a) {
      foreach ($nodes as $key => $value) {
        $node_first_letter = substr($value->getTitle(), 0, 1);
        if ($is_single_a && ($node_first_letter !== 'A' && $node_first_letter !== 'a')) {
          unset($nodes[$key]);
        }
        if ($is_double_a && ($node_first_letter === 'A' || $node_first_letter === 'a')) {
          unset($nodes[$key]);
        }
      }
    }

    $lexiconArticles = $this->entityTypeManager->getViewBuilder('node')
      ->viewMultiple($nodes, 'list');

    return [
      '#theme' => 'lexicon',
      '#articles' => $lexiconArticles,
      '#letter' => $initialLetter,
      '#count' => count($lexiconArticlesIds),
      '#max_count' => (int) $numberOfArticles
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
