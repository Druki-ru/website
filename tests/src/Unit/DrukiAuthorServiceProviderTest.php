<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\druki_author\DrukiAuthorServiceProvider;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Provides test for druki author service provider.
 *
 * @coversDefaultClass \Drupal\druki_author\DrukiAuthorServiceProvider
 */
final class DrukiAuthorServiceProviderTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that event subscriber registered as expected.
   */
  public function testContentSourceEventSubscriber(): void {
    $container = $this->buildContainer();

    $service_provider = new DrukiAuthorServiceProvider();
    $service_provider->alter($container);
    $this->assertFalse($container->has('druki_author.event_subscriber.content_source'));

    $container->addDefinitions([
      'druki_content.repository.content_source_settings' => new Definition(),
    ]);
    $service_provider->alter($container);
    $this->assertTrue($container->has('druki_author.event_subscriber.content_source'));
  }

  /**
   * Builds a mock of service container.
   *
   * @return \Drupal\Core\DependencyInjection\ContainerBuilder
   *   The service container mock.
   */
  protected function buildContainer(): ContainerBuilder {
    $services = [];

    $container = $this->prophesize(ContainerBuilder::class);
    $container->addDefinitions(Argument::type('array'))->will(function (array $args) use (&$services) {
      foreach ($args[0] as $id => $definition) {
        $services[$id] = $definition;
      }
    });
    $container->has(Argument::type('string'))->will(function (array $args) use (&$services) {
      return \array_key_exists($args[0], $services);
    });
    return $container->reveal();
  }

}
