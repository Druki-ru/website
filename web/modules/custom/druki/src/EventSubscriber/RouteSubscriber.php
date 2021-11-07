<?php

declare(strict_types=1);

namespace Drupal\druki\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\search\SearchPageRepositoryInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides route subscriber to alter existing routes.
 */
final class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The search page repository.
   */
  protected SearchPageRepositoryInterface $searchPageRepository;

  /**
   * Constructs a new RouteSubscriber object.
   *
   * @param \Drupal\search\SearchPageRepositoryInterface $search_page_repository
   *   The search page repository.
   */
  public function __construct(SearchPageRepositoryInterface $search_page_repository) {
    $this->searchPageRepository = $search_page_repository;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection): void {
    $this->alterSearchRoutes($collection);
  }

  /**
   * Does alterations realted to Search module and project needs.
   *
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   The route collection.
   */
  protected function alterSearchRoutes(RouteCollection $collection): void {
    if ($route = $collection->get('search.view')) {
      // Replace default controller for /search page with ours. We want to use
      // /search page, instead /search/{type}.
      $route->setDefault('_controller', '\Drupal\druki\Controller\SearchController::view');
    }

    // We can't remove view and help pages because it will break administrative
    // pages. Instead of removing them, we restrict access to admin only.
    $active_pages = $this->searchPageRepository->getActiveSearchPages();
    foreach (\array_keys($active_pages) as $entity_id) {
      if ($route = $collection->get("search.view_$entity_id")) {
        $route->setRequirement('_permission', 'administer site configuration');
      }
      if ($route = $collection->get("search.help_$entity_id")) {
        $route->setRequirement('_permission', 'administer site configuration');
      }
    }
  }

}
