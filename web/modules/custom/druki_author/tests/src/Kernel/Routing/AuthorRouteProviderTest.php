<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\Kernel\Routing;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\druki_author\Routing\AuthorRouteProvider;
use Drupal\Tests\druki_author\Kernel\DrukiAuthorKernelTestBase;
use Symfony\Component\Routing\Route;

/**
 * Provides test for author entity route provider.
 *
 * @coversDefaultClass \Drupal\druki_author\Routing\AuthorRouteProvider
 */
final class AuthorRouteProviderTest extends DrukiAuthorKernelTestBase {

  /**
   * The 'druki_author' entity type definition.
   */
  protected ?ContentEntityTypeInterface $authorEntityType;

  /**
   * Tests that all needed routes provided.
   */
  public function testRoutes(): void {
    $author_route_provider = new AuthorRouteProvider();
    $collection = $author_route_provider->getRoutes($this->authorEntityType);
    $canonical = $collection->get('entity.druki_author.canonical');
    $this->assertInstanceOf(Route::class, $canonical);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->authorEntityType = $this->container->get('entity_type.manager')
      ->getDefinition('druki_author');
  }

}
