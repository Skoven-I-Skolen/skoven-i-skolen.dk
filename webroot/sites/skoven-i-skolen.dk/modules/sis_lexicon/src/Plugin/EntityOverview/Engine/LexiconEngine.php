<?php
namespace Drupal\sis_lexicon\Plugin\EntityOverview\Engine;

use Drupal\entity_overview\Entity\Overview;
use Drupal\entity_overview\OverviewFieldInfoInterface;
use Drupal\entity_overview\OverviewFields\OwnerField;
use Drupal\entity_overview\OverviewFields\SearchTextField;
use Drupal\entity_overview\OverviewFilter;
use Drupal\entity_overview\Plugin\EntityOverview\Engine\EntityQueryEngine;
use Drupal\node\Entity\Node;
use Drupal\sis_lexicon\OverviewFields\LetterField;

/**
 * @Engine(
 *  id = "lexicon",
 *  title = "Lexicon",
 *  facets = {
 *    "text",
 *    "letter",
 *    "count",
 *    "sort",
 *    "pagination"
 *  },
 *  multiple = false,
 *  recommendations = false
 * )
 */
class LexiconEngine extends EntityQueryEngine {

  public $sqlFriendlyChars = ['A','B','C','D','E','F','G','H','I','J','K','L','M',
    'N','O','P','Q','R','S','T','U','V','W','X','Y','Z','Æ','Ø'];

  /**
   * Returns info about an engine field.
   *
   * @param \Drupal\entity_overview\Entity\Overview $overview
   * @param string $field
   *
   * @return array
   */
  protected function getEngineFieldInfo(Overview $overview, string $field): ?OverviewFieldInfoInterface {
    return match ($field) {
      'text' => new SearchTextField(),
      'owner' => new OwnerField(),
      'letter' => new LetterField(),
      default => NULL
    };
  }

  /**
   * @inheritDoc
   */
  public function getResult(OverviewFilter $filter) {
    $firstLetter = $filter->getFieldValue('letter');
    $isSqlFriendly = empty($firstLetter) || in_array($firstLetter, $this->sqlFriendlyChars);
    $overview = $filter->getOverview();
    $storage = $this->entityTypeManager->getStorage($this->getEntityTypeID($overview));
    $keys = $this->entityTypeManager->getDefinition($this->getEntityTypeID($overview))->getKeys();
    $query = $storage->getQuery()
      ->condition($keys['bundle'], $this->getBundles($overview), 'IN')
      ->condition('field_article_type', 15)
      ->condition('status', 1);
    foreach ($filter->getFieldValues() as $field_name => $value) {
      if (empty($value)) {
        continue;
      }

      if ($field_name == 'text') {
        $query->condition($keys['label'], $value, 'CONTAINS');
      } elseif ($field_name == 'letter') {
        $query->condition($keys['label'], $value, 'STARTS_WITH');
      } elseif (is_array($value)) {
        $query->condition($field_name, $value, 'IN');
      } else {
        $query->condition($field_name, $value);
      }
    }
    if ($filter->hasPagination()) {
      // Do not use dependency injection for the request, or it will be serialized with the form state
      \Drupal::requestStack()->getCurrentRequest()->query->set('page', $filter->getPage());
      $query->pager($filter->getCount());
    } elseif ($filter->getCount() > 0) {
      $query->range($filter->getPage() * $filter->getCount(), $filter->getCount());
    }
    switch ($filter->getSort()) {
      case 'alphabetical':
        $query->sort($keys['label'], 'ASC');
        break;
      case 'oldest':
        $query->sort($overview->getSortField(), 'ASC');
        break;
      default:
        $query->sort($overview->getSortField(), 'DESC');
        break;
    }
    // The code below is a solution to MySQL not recognizing Å.
    // It's a very hacky solution that's unfortunately necessary because we can't
    // change the table collation at this point.
    if (empty($firstLetter)) {
      $results = $query->execute();
      if ($filter->getPage() == '0') {
        $final_results = [];
        foreach ($results as $key => $value) {
          $node = Node::load($value);
          if ($node) {
            if (in_array(substr($node->getTitle(), 0, 1), $this->sqlFriendlyChars)) {
              $final_results[] = $value;
            }
          }
        }
        return $final_results;
      }
      else if (count($results) < $filter->getCount()) {
        // We are on the last page.
        $nodes_with_aa = $storage->getQuery()
          ->condition('title', 'Å', 'STARTS_WITH')
          ->condition('status', 1)
          ->condition('field_article_type', 15)
          ->execute();
        $results = array_merge($nodes_with_aa, $results);
        $isSqlFriendly = FALSE;
      }
      else {
        return $results;
      }
    }
    if ($isSqlFriendly) {
      if (!$results) {
        $results = $query->execute();
      }
      if (strtoupper($firstLetter) !== 'A') {
        return $results;
      }
      $final_results = [];
      foreach ($results as $result) {
        $node = Node::load($result);
        if ($node) {
          if (in_array(substr($node->getTitle(), 0, 1), $this->sqlFriendlyChars)) {
            $final_results[] = $result;
          }
        }
      }
      return $final_results;
    }
    else {
      if (!$results) {
        $results = $query->execute();
      }
      $final_results = [];
      foreach ($results as $result) {
        $node = Node::load($result);
        if ($node) {
          if (!in_array(substr($node->getTitle(), 0, 1), $this->sqlFriendlyChars)) {
            $final_results[] = $result;
          }
        }
      }
      return $final_results;
    }
  }

  /**
   * @inheritDoc
   */
  public function getResultsCount(OverviewFilter $filter) {
    $overview = $filter->getOverview();
    $keys = $this->entityTypeManager->getDefinition($this->getEntityTypeID($overview))->getKeys();
    $query = $this->entityTypeManager->getStorage($this->getEntityTypeID($overview))->getQuery()
      ->condition($keys['bundle'], $this->getBundles($overview), 'IN')
      ->condition('status', 1)
      ->condition('field_article_type', 15)
      ->count();
    foreach ($filter->getFieldValues() as $field_name => $value) {
      if (empty($value)) {
        continue;
      }
      if ($field_name == 'text') {
        $query->condition($keys['label'], $value, 'CONTAINS');
      } elseif ($field_name == 'letter') {
        $query->condition($keys['label'], $value, 'STARTS_WITH');
      } elseif (is_array($value)) {
        $query->condition($field_name, $value, 'IN');
      } else {
        $query->condition($field_name, $value);
      }
    }
    return $query->execute();
  }
}
