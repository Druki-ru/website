<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Unit\DependencyInjection;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\druki\DependencyInjection\RegisterDrukiEntitySyncQueueCompilerPass;
use Drupal\druki\DrukiServiceProvider;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for service container alterations provided by druki module.
 *
 * @coversDefaultClass \Drupal\druki\DrukiServiceProvider
 */
final class DrukiServiceProviderTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that registration does what we expected.
   */
  public function testRegister(): void {
    $container = $this->buildContainer();

    $provider = new DrukiServiceProvider();
    $provider->register($container);
  }

  /**
   * Builds mock of container.
   *
   * @return \Drupal\Core\DependencyInjection\ContainerBuilder
   *   The mock of container.
   */
  protected function buildContainer(): ContainerBuilder {
    $container = $this->prophesize(ContainerBuilder::class);
    $container->addCompilerPass(Argument::type(RegisterDrukiEntitySyncQueueCompilerPass::class))
      ->shouldBeCalled();
    return $container->reveal();
  }

}
