<?php

namespace Drupal\sis_news\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\Routing\Route;

class NewsBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  public function applies(RouteMatchInterface $route_match) {
    if (!$route_entity = $this->getEntityFromRouteMatch($route_match)) {
      return FALSE;
    }

    return ($route_entity->bundle() === 'news');
  }

  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addCacheContexts(['route']);

    /** @var \Drupal\Core\Entity\ContentEntityInterface $route_entity */
    $route_entity = $this->getEntityFromRouteMatch($route_match);

    $links = [
      Link::createFromRoute($this->t('Home'), '<front>'),
      Link::createFromRoute($this->t('Nyheder', [], ['context' => 'breadcrumb']), 'entity.node.canonical', ['node' => 1]),
      Link::createFromRoute($route_entity->label(), '<none>'),
    ];

    $breadcrumb->setLinks($links);
    return $breadcrumb;
  }

  /**
   * Return the entity type id from a route object.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route object.
   *
   * @return string|null
   *   The entity type id, null if it doesn't exist.
   */
  protected function getEntityTypeFromRoute(Route $route): ?string {
    if (!empty($route->getOptions()['parameters'])) {
      foreach ($route->getOptions()['parameters'] as $option) {
        if (isset($option['type']) && strpos($option['type'], 'entity:') === 0) {
          return substr($option['type'], strlen('entity:'));
        }
      }
    }

    return NULL;
  }

  /**
   * Returns an entity parameter from a route match object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity, or null if it's not an entity route.
   */
  protected function getEntityFromRouteMatch(RouteMatchInterface $route_match): ?EntityInterface {
    $route = $route_match->getRouteObject();
    if (!$route) {
      return NULL;
    }

    $entity_type_id = $this->getEntityTypeFromRoute($route);
    if ($entity_type_id) {
      return $route_match->getParameter($entity_type_id);
    }

    return NULL;
  }

}
