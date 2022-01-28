<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\ExistingSite\Routing;

use Drupal\druki_author\Routing\AuthorRouteProvider;
use Symfony\Component\Routing\Route;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for author entity route provider.
 *
 * @coversDefaultClass \Drupal\druki_author\Routing\AuthorRouteProvider
 */
final class AuthorRouteProviderTest extends ExistingSiteBase {

  /**
   * Tests that all needed routes provided.
   */
  public function testRoutes(): void {
    $entity_type = $this->container->get('entity_type.manager')
      ->getDefinition('druki_author');

    $author_route_provider = new AuthorRouteProvider();
    $collection = $author_route_provider->getRoutes($entity_type);

    $canonical = $collection->get('entity.druki_author.canonical');
    $this->assertInstanceOf(Route::class, $canonical);
  }

}
