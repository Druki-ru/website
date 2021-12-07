<?php

declare(strict_types=1);

namespace Drupal\druki_author\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides author entity route provider.
 */
final class AuthorRouteProvider implements EntityRouteProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type): RouteCollection {
    $collection = new RouteCollection();

    $entity_type_id = $entity_type->id();

    if ($canonical_route = $this->getCanonicalRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.canonical", $canonical_route);
    }

    return $collection;
  }

  /**
   * Gets the canonical route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route
   *   The generated route.
   */
  protected function getCanonicalRoute(EntityTypeInterface $entity_type): Route {
    $entity_type_id = $entity_type->id();
    $route = new Route($entity_type->getLinkTemplate('canonical'));
    $route->addDefaults([
        '_entity_view' => "{$entity_type_id}.full",
        '_title_callback' => '\Drupal\Core\Entity\Controller\EntityController::title',
      ])
      ->setRequirement('_access', 'TRUE')
      ->setOption('parameters', [
        $entity_type_id => ['type' => 'entity:' . $entity_type_id],
      ]);
    return $route;
  }

}
