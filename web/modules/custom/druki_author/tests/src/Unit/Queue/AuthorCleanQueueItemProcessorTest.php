<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\Unit\Queue;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;
use Drupal\druki_author\Data\AuthorCleanQueueItem;
use Drupal\druki_author\Queue\AuthorCleanQueueItemProcessor;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\Exception\Prediction\FailedPredictionException;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides author clean queue item processor test.
 *
 * @coversDefaultClass \Drupal\druki_author\Queue\AuthorCleanQueueItemProcessor
 */
final class AuthorCleanQueueItemProcessorTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that is applicable process only expected value objects.
   */
  public function testIsApplicable(): void {
    $processor = new AuthorCleanQueueItemProcessor(
      $this->buildEntityTypeManager([]),
      $this->buildQueueManager([]),
    );

    $invalid = new class() implements EntitySyncQueueItemInterface {

      // @phpcs:ignore Drupal.Commenting.FunctionComment.Missing
      public function getPayload(): mixed {
        return NULL;
      }

    };
    $this->assertFalse($processor->isApplicable($invalid));

    $valid = new AuthorCleanQueueItem();
    $this->assertTrue($processor->isApplicable($valid));
  }

  /**
   * Builds a mock for entity type manager.
   *
   * @param array $storage_entity_ids
   *   The entity storage return IDs.
   * @param array $expected_entity_ids_to_delete
   *   An array with expected entities to delete.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   A mock of entity type manager.
   */
  protected function buildEntityTypeManager(array $storage_entity_ids, array $expected_entity_ids_to_delete = []): EntityTypeManagerInterface {
    $query = $this->prophesize(QueryInterface::class);
    $query->accessCheck(Argument::any())->willReturn($query->reveal());
    $query->execute()->willReturn($storage_entity_ids);

    $author_storage = $this->prophesize(EntityStorageInterface::class);
    $author_storage->getQuery()->willReturn($query->reveal());
    $author_storage->loadMultiple(Argument::type('array'))->will(static fn (array $args) => $args[0]);
    if (empty($expected_entity_ids_to_delete)) {
      $author_storage->delete(Argument::type('array'))->shouldNotBeCalled();
    }
    else {
      $author_storage->delete(Argument::type('array'))
        ->should(static function (array $calls) use ($expected_entity_ids_to_delete): void {
          /** @var \Prophecy\Call\Call $first_call */
          $first_call = $calls[0];
          $call_ids = \array_values($first_call->getArguments()[0]);
          if ($call_ids != $expected_entity_ids_to_delete) {
            throw new FailedPredictionException('Author storage delete called with wrong IDs.');
          }
        });
    }

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('druki_author')->willReturn($author_storage->reveal());
    return $entity_type_manager->reveal();
  }

  /**
   * Builds a mock for queue manager.
   *
   * @param array $state_entity_ids
   *   The state entity stored IDs.
   *
   * @return \Drupal\druki\Queue\EntitySyncQueueManagerInterface
   *   A mock of queue manager.
   */
  protected function buildQueueManager(array $state_entity_ids): EntitySyncQueueManagerInterface {
    $state = $this->prophesize(EntitySyncQueueStateInterface::class);
    $state->getEntityIds()->willReturn($state_entity_ids);

    $queue_manager = $this->prophesize(EntitySyncQueueManagerInterface::class);
    $queue_manager->getState()->willReturn($state->reveal());

    return $queue_manager->reveal();
  }

  /**
   * Tests that processor works as expected.
   */
  public function testProcess(): void {
    $processor = new AuthorCleanQueueItemProcessor(
      $this->buildEntityTypeManager([1, 2, 3]),
      $this->buildQueueManager([1, 2, 3]),
    );
    $processor->process(new AuthorCleanQueueItem());

    $processor = new AuthorCleanQueueItemProcessor(
      $this->buildEntityTypeManager([1, 2, 3], [3]),
      $this->buildQueueManager([1, 2, 4]),
    );
    $processor->process(new AuthorCleanQueueItem());
  }

}
