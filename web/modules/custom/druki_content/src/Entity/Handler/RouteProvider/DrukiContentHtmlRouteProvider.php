<?php

namespace Drupal\druki_content\Entity\Handler\RouteProvider;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides HTML routes for druki content pages.
 */
final class DrukiContentHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    /** @var \Symfony\Component\Routing\RouteCollection $collection */
    $collection = parent::getRoutes($entity_type);
    $entity_type_id = $entity_type->id();

    if ($remote_edit_route = $this->getEditRemoteRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.edit_remote", $remote_edit_route);
    }

    if ($remote_history_route = $this->getHistoryRemoteRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.history_remote", $remote_history_route);
    }

    // Removes possibility to add new content via admin UI. This entity type is
    // designed to be created from remote sources.
    $collection->remove("entity.{$entity_type_id}.add_form");

    if ($delete_all_route = $this->getDeleteAllRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.delete_all", $delete_all_route);
    }

    if ($settings_route = $this->getSettingsRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.settings", $settings_route);
    }

    if ($sync_route = $this->getSyncRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.sync", $sync_route);
    }

    return $collection;
  }

  /**
   * Gets synchronization form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route.
   */
  protected function getSyncRoute(EntityTypeInterface $entity_type): ?Route {
    if ($entity_type->hasLinkTemplate('sync') && $entity_type->hasHandlerClass('form', 'sync')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('sync'));
      $route
        ->setDefault('_form', $entity_type->getFormClass('sync'))
        ->setRequirement('_permission', "administer {$entity_type_id}");

      return $route;
    }

    return NULL;
  }

  /**
   * Gets settings form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route.
   */
  protected function getSettingsRoute(EntityTypeInterface $entity_type): ?Route {
    if ($entity_type->hasLinkTemplate('settings') && $entity_type->hasHandlerClass('form', 'settings')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('settings'));
      $route
        ->setDefault('_form', $entity_type->getFormClass('settings'))
        ->setRequirement('_permission', "administer {$entity_type_id}");

      return $route;
    }

    return NULL;
  }

  /**
   * Gets delete all form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route.
   */
  protected function getDeleteAllRoute(EntityTypeInterface $entity_type): ?Route {
    if ($entity_type->hasLinkTemplate('delete-all-form') && $entity_type->hasHandlerClass('form', 'delete-all')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('delete-all-form'));
      $route
        ->setDefault('_form', $entity_type->getFormClass('delete-all'))
        ->setRequirement('_permission', "delete {$entity_type_id}");

      return $route;
    }

    return NULL;
  }

  /**
   * Gets edit remote route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getEditRemoteRoute(EntityTypeInterface $entity_type): ?Route {
    if ($entity_type->hasLinkTemplate('edit-remote') && $this->hasRedirectControllerClass($entity_type)) {
      $entity_type_id = $entity_type->id();
      $controller_class = $entity_type->getHandlerClass('redirect_controller');
      $route = new Route($entity_type->getLinkTemplate('edit-remote'));
      $route
        ->setDefault('_controller', "$controller_class::build")
        ->setDefault('redirect_to', 'edit')
        ->setRequirement('_entity_access', "{$entity_type_id}.view")
        ->setOption('parameters', [
          $entity_type_id => ['type' => 'entity:' . $entity_type_id],
        ]);

      if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
        $route->setRequirement($entity_type_id, '\d+');
      }

      return $route;
    }

    return NULL;
  }

  /**
   * Gets is current entity type has registered redirect controller.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to check for.
   *
   * @return bool
   *   TRUE if handlers is registered, FALSE otherwise.
   */
  protected function hasRedirectControllerClass(EntityTypeInterface $entity_type): bool {
    return $entity_type->hasHandlerClass('redirect_controller');
  }

  /**
   * Gets edit remote route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getHistoryRemoteRoute(EntityTypeInterface $entity_type): ?Route {
    if ($entity_type->hasLinkTemplate('history-remote') && $this->hasRedirectControllerClass($entity_type)) {
      $entity_type_id = $entity_type->id();
      $controller_class = $entity_type->getHandlerClass('redirect_controller');
      $route = new Route($entity_type->getLinkTemplate('history-remote'));
      $route
        ->setDefault('_controller', "$controller_class::build")
        ->setDefault('redirect_to', 'history')
        ->setRequirement('_entity_access', "{$entity_type_id}.view")
        ->setOption('parameters', [
          $entity_type_id => ['type' => 'entity:' . $entity_type_id],
        ]);

      if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
        $route->setRequirement($entity_type_id, '\d+');
      }

      return $route;
    }

    return NULL;
  }

}
