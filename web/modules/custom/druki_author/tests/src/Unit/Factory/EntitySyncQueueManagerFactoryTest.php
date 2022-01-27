<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\Unit\Factory;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\druki\Factory\EntitySyncQueueManagerFactory;
use Drupal\druki\Factory\EntitySyncQueueStateFactoryInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for entity sync queue manager factory.
 *
 * @coversDefaultClass \Drupal\druki\Factory\EntitySyncQueueManagerFactory
 */
final class EntitySyncQueueManagerFactoryTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that factory works as expected.
   */
  public function testFactory(): void {
    $queue_manager_factory = new EntitySyncQueueManagerFactory($this->buildQueueFactory(), $this->buildQueueStateFactory());
    $first = $queue_manager_factory->get('first');
    $this->assertInstanceOf(EntitySyncQueueManagerInterface::class, $first);
    $second = $queue_manager_factory->get('second');
    $this->assertNotSame($first, $second);
    $third = $queue_manager_factory->get('first');
    $this->assertSame($first, $third);
  }

  /**
   * Builds mock of queue factory.
   *
   * @return \Drupal\Core\Queue\QueueFactory
   *   The mock of queue factory.
   */
  protected function buildQueueFactory(): QueueFactory {
    $queue = $this->prophesize(QueueInterface::class);

    $queue_factory = $this->prophesize(QueueFactory::class);
    $queue_factory->get(Argument::type('string'))->willReturn($queue->reveal());
    return $queue_factory->reveal();
  }

  /**
   * Builds mock of queue state factory.
   *
   * @return \Drupal\druki\Factory\EntitySyncQueueStateFactoryInterface
   *   The mock of queue state factory.
   */
  protected function buildQueueStateFactory(): EntitySyncQueueStateFactoryInterface {
    $queue_state = $this->prophesize(EntitySyncQueueStateInterface::class);
    $queue_state_factory = $this->prophesize(EntitySyncQueueStateFactoryInterface::class);
    $queue_state_factory->get(Argument::type('string'))->willReturn($queue_state->reveal());
    return $queue_state_factory->reveal();
  }

}
