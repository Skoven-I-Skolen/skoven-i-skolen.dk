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

  public function getFilters() {
    $alphapet = array_merge(range('A', 'Z'), ['Æ', 'Ø', 'Å']);
    $alphapet_links = [];

    foreach ($alphapet as $letter) {
      $alphapet_links[] = Link::createFromRoute($letter, 'sis_lexicon.get_articles',
        ['letter' => $letter],
        ['attributes' => ['class' => ['use-ajax']],]
      );
    }

    return [
      '#theme' => 'item_list',
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
  public function getArticles(string $initialLetter): ?array {
    if (!$lexiconArticlesIds = $this->lexiconRepository->getEntityIdsByInitialLetter($initialLetter)) {
      return NULL;
    }

    $nodes = Node::loadMultiple($lexiconArticlesIds);
    $lexiconArticles = $this->entityTypeManager->getViewBuilder('node')
      ->viewMultiple($nodes, 'list');

    return [
      '#theme' => 'lexicon',
      '#articles' => $lexiconArticles,
    ];
  }

}
