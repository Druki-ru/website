<?php

namespace Drupal\druki_category\Service;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Class CategoryNavigation for building category navigation links.
 *
 * @package Drupal\druki_category
 */
class CategoryNavigation {

  /**
   * The route matcher.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * CategoryNavigation constructor.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    RouteMatchInterface $route_match,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Trying to prepare links on route object parameters.
   *
   * @return \Drupal\Core\Link[]|null
   *   The array with links, NULL if not found any of them.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function getCategoryLinksFromRoute(): ?array {
    $category_area = $this->getCategoryAreaFromRoute();

    if ($category_area) {
      $entity = $this->getEntityWithCategoryFromRoute();

      return $this->getLinksByCategoryArea($entity, $category_area);
    }

    return NULL;
  }

  /**
   * Trying to get category area name from current route.
   *
   * @return string|null
   *   The category name, NULL if not found anything.
   */
  public function getCategoryAreaFromRoute(): ?string {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__);

    if (!isset($result)) {
      $entity = $this->getEntityWithCategoryFromRoute();

      if ($entity) {
        $field_name = $this->findDrukiCategoryFieldName($entity);

        if (!$entity->get($field_name)->isEmpty()) {
          $result = $entity->get($field_name)->area;
        }
      }
    }

    return $result;
  }

  /**
   * Gets first content entity which has druki_category field type.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   The entity with druki_category field, NULL if not found.
   */
  public function getEntityWithCategoryFromRoute(): ?ContentEntityInterface {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__);

    if (!isset($result)) {
      foreach ($this->routeMatch->getParameters() as $parameter) {
        if ($parameter instanceof ContentEntityInterface) {
          if ($this->findDrukiCategoryFieldName($parameter)) {
            $result = $parameter;
            continue;
          }
        }
      }
    }

    return $result;
  }

  /**
   * Gets druki_category field name from entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to be processed.
   *
   * @return string|null
   *   The field name, NULL if entity is not contain any druki_content fields.
   */
  protected function findDrukiCategoryFieldName(ContentEntityInterface $entity): ?string {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $entity->getEntityTypeId() . ':' . $entity->id());

    if (!isset($result)) {
      foreach ($entity->getFieldDefinitions() as $field_definition) {
        if ($field_definition->getType() == 'druki_category') {
          $result = $field_definition->getName();
        }
      }
    }

    return $result;
  }

  /**
   * Gets links for specific entity type and category area.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity object with category field. Links will be with the same
   *   entity type.
   * @param string $category_area
   *   The category area name.
   *
   * @return \Drupal\Core\Link[]|null
   *    The array with links, NULL if not found anything.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function getLinksByCategoryArea(ContentEntityInterface $entity, string $category_area): ?array {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $category_area);

    if (!isset($result)) {
      $field_name = $this->findDrukiCategoryFieldName($entity);
      $entity_storage = $this->entityTypeManager->getStorage($entity->getEntityTypeId());

      $result = $entity_storage
        ->getQuery()
        ->condition($field_name . '.area', $category_area)
        ->sort($field_name . '.order', 'ASC')
        ->execute();

      if (!empty($result)) {
        $entities = $entity_storage->loadMultiple($result);

        $result = [];

        foreach ($entities as $entity) {
          $text = $entity->get($field_name)->title ?? $entity->label();

          $result[] = [
            'url' => $entity->toUrl('canonical'),
            'text' => $text,
          ];
        }
      }
    }

    return $result;
  }

}
