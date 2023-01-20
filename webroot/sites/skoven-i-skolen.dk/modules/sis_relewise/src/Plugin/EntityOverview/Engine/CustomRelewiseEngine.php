<?php
namespace Drupal\sis_relewise\Plugin\EntityOverview\Engine;

use Drupal\entity_overview\Entity\Overview;
use Drupal\entity_overview\OverviewFieldInfoInterface;
use Drupal\entity_overview\OverviewFilter;
use Drupal\entity_overview\OverviewResultInterface;
use Drupal\relewise\Plugin\EntityOverview\Engine\RelewiseEngine;
use Drupal\relewise\RelewiseOverviewResult;
use Drupal\sis_relewise\OverviewFields\ExternalField;

/**
 * @Engine(
 *  id = "custom_relewise",
 *  title = "Custom Relewise",
 *  facets = {
 *    "text",
 *    "owner",
 *    "external",
 *    "count",
 *    "sort",
 *    "pagination"
 *  },
 *  multiple = false,
 *  recommendations = false
 * )
 */
class CustomRelewiseEngine extends RelewiseEngine {

  /**
   * @inheritDoc
   */
  protected function getEngineFieldInfo(Overview $overview, string $field): ?OverviewFieldInfoInterface {
    return match ($field) {
      'external' => new ExternalField(),
      default => parent::getEngineFieldInfo($overview, $field)
    };
  }

  /**
   * @inheritDoc
   */
  protected function getSearchFields(OverviewFilter $filter) {
    $fields = parent::getSearchFields($filter);
    unset($fields['external']);
    if (!empty($filter->getFieldValue('external'))) {
      $fields['type'] = 'node';
      $fields['bundle'] = 'link_article';
    }
    return $fields;
  }

  /**
   * @inheritDoc
   */
  public function getOverviewResult(OverviewFilter $filter): OverviewResultInterface {
    return new RelewiseOverviewResult($this, $filter);
  }
}
