<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Repository;

use Drupal\Core\State\StateInterface;
use Drupal\druki\Repository\EntitySyncQueueState;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for default implementation of entity sync queue state storage.
 *
 * @coversDefaultClass \Drupal\druki\Repository\EntitySyncQueueState
 */
final class EntitySyncQueueStateTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * The internal state storage for mocking.
   */
  protected array $stateStorage = [];

  /**
   * The mock of key/value state storage.
   */
  protected StateInterface $state;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $self = $this;
    $state = $this->prophesize(StateInterface::class);
    $state->get(Argument::type('string'), Argument::any())->will(function ($args) use ($self) {
      $storage_key = $args[0];
      $default_value = $args[1];
      if (!isset($self->stateStorage[$storage_key])) {
        return $default_value;
      }
      return $self->stateStorage[$storage_key];
    });
    $state->set(Argument::type('string'), Argument::any())->will(function ($args) use ($self) {
      $storage_key = $args[0];
      $value = $args[1];
      $self->stateStorage[$storage_key] = $value;
    });
    $state->delete(Argument::type('string'))->will(function ($args) use ($self) {
      $storage_key = $args[0];
      unset($self->stateStorage[$storage_key]);
    });
    $this->state = $state->reveal();
  }

  /**
   * Tests that repository works as expected.
   */
  public function testRepository(): void {
    $state = new EntitySyncQueueState($this->state, 'test_1');
    $this->assertEmpty($state->getEntityIds());
    $state->storeEntityIds([1, 2, 3]);
    $this->assertEquals([1, 2, 3], $state->getEntityIds());
    $state->storeEntityIds([2, 3, 4]);
    $this->assertEquals([1, 2, 3, 4], $state->getEntityIds());
    $state->delete();
    $this->assertEmpty($state->getEntityIds());
  }

}
