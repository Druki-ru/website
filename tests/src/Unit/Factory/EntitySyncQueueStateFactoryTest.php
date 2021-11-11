<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Factory;

use Drupal\Core\State\StateInterface;
use Drupal\druki\Factory\EntitySyncQueueStateFactory;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for entity sync queue state factory.
 *
 * @coversDefaultClass \Drupal\druki\Factory\EntitySyncQueueStateFactory
 */
final class EntitySyncQueueStateFactoryTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * The mock of key/value state storage.
   */
  protected StateInterface $state;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $state = $this->prophesize(StateInterface::class);
    $this->state = $state->reveal();
  }

  /**
   * Tests that factory works as expected.
   */
  public function testFactory(): void {
    $factory = new EntitySyncQueueStateFactory($this->state);

    $state_1 = $factory->get('test_1');
    $this->assertInstanceOf(EntitySyncQueueStateInterface::class, $state_1);
    // Make sure that second call return previously created instance, not the
    // new one.
    $this->assertEquals($state_1, $factory->get('test_1'));

    $state_2 = $factory->get('test_2');
    $this->assertNotEquals($state_1, $state_2);
  }

}
