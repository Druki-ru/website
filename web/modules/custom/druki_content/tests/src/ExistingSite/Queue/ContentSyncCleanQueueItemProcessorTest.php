<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\ExistingSite\Queue;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;
use Drupal\druki_content\Data\ContentSyncCleanQueueItem;
use Drupal\druki_content\Queue\ContentSyncCleanQueueItemProcessor;
use Drupal\druki_content\Repository\ContentStorage;
use Drupal\Tests\druki\Trait\EntityCleanupTrait;
use Drupal\Tests\druki_content\Trait\DrukiContentCreationTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content sync queue cleanup processor.
 *
 * @coversDefaultClass \Drupal\druki_content\Queue\ContentSyncCleanQueueItemProcessor
 */
final class ContentSyncCleanQueueItemProcessorTest extends ExistingSiteBase {

  use EntityCleanupTrait;
  use DrukiContentCreationTrait;

  /**
   * The clean queue item processor.
   */
  protected ContentSyncCleanQueueItemProcessor $processor;

  /**
   * The content storage.
   */
  protected ContentStorage $contentStorage;

  /**
   * The content sync queue state.
   */
  protected EntitySyncQueueStateInterface $queueState;

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    $this->queueState->delete();
    parent::tearDown();
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->processor = $this->container->get('druki_content.queue.clean_queue_processor');
    $this->contentStorage = $this->container->get('entity_type.manager')->getStorage('druki_content');
    $this->queueState = $this->container->get('druki_content.repository.content_sync_queue_state');
    $this->storeEntityIds(['druki_content']);
  }

  /**
   * Tests that processor works as expected.
   */
  public function testProcessor(): void {
    $existing_ids = $this->contentStorage->getQuery()
      ->accessCheck(FALSE)
      ->execute();
    // Save currently existed content IDs emulating that all created content
    // after this process will be consider as outdated and missing in source.
    $this->queueState->storeEntityIds($existing_ids);
    $outdated_content = $this->createDrukiContent(['type' => 'documentation']);
    $outdated_content_id = $outdated_content->id();

    $count = $this->contentStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('internal_id', $outdated_content_id)
      ->count()
      ->execute();
    $this->assertEquals(1, $count);

    $queue_item = new ContentSyncCleanQueueItem();
    $this->processor->process($queue_item);
    // Since we do not add entity ID created above into state storage, it
    // must be deleted.
    $count = $this->contentStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('internal_id', $outdated_content_id)
      ->count()
      ->execute();
    $this->assertEquals(0, $count);
    // Make sure that previously existed entities still available.
    $new_ids = $this->contentStorage->getQuery()
      ->accessCheck(FALSE)
      ->execute();
    $this->assertEquals($existing_ids, $new_ids);
  }

  /**
   * Tests that method works as expected.
   *
   * @covers ::isApplicable
   */
  public function testIsApplicable(): void {
    $invalid_item = new class() implements EntitySyncQueueItemInterface {

      // @phpcs:ignore Drupal.Commenting.FunctionComment.Missing
      public function getPayload(): mixed {
        return 'foo';
      }

    };

    $this->assertFalse($this->processor->isApplicable($invalid_item));

    $valid_item = new ContentSyncCleanQueueItem();
    $this->assertTrue($this->processor->isApplicable($valid_item));
  }

}
