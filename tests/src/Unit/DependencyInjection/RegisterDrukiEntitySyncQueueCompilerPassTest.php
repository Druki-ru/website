<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\DependencyInjection;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\druki\DependencyInjection\RegisterDrukiEntitySyncQueueCompilerPass;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Provides test for druki entity sync queue tagged services compiler pass.
 *
 * @coversDefaultClass \Drupal\druki\DependencyInjection\RegisterDrukiEntitySyncQueueCompilerPass
 */
final class RegisterDrukiEntitySyncQueueCompilerPassTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that compiler works as expected.
   */
  public function testProcess(): void {
    $compiler = new RegisterDrukiEntitySyncQueueCompilerPass();
    $container = $this->buildContainer();
    $compiler->process($container);
    $this->assertEquals(['first', 'second'], $container->getParameter('druki.entity_sync_queues'));
  }

  /**
   * Builds mock of container builder.
   *
   * @return \Drupal\Core\DependencyInjection\ContainerBuilder
   *   The mock of container builder.
   */
  protected function buildContainer(): ContainerBuilder {
    $container = $this->prophesize(ContainerBuilder::class);
    $found_services = [
      'foo_bar.first' => [['queue_name' => 'first']],
      'foo_bar.second' => [['queue_name' => 'second']],
      // This one doesn't specify queue_name and should be skipped.
      'foo_bar.third' => [],
    ];
    $container->findTaggedServiceIds('druki_entity_sync_queue')->willReturn($found_services);

    $first_service_definition = $this->prophesize(Definition::class);
    $first_service_definition->setArgument(0, 'first')->shouldBeCalled();

    $second_service_definition = $this->prophesize(Definition::class);
    $second_service_definition->setArgument(0, 'second')->shouldBeCalled();

    $container->getDefinition('foo_bar.first')->willReturn($first_service_definition->reveal());
    $container->getDefinition('foo_bar.second')->willReturn($second_service_definition->reveal());

    $container->setParameter('druki.entity_sync_queues', Argument::type('array'))->will(function ($args) use ($container) {
      $container->getParameter('druki.entity_sync_queues')->willReturn($args[1]);
    });

    return $container->reveal();
  }

}
