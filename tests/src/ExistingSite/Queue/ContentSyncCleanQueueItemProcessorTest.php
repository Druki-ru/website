<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Druki\Tests\Traits\EntityCleanupTrait;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;
use Drupal\druki_content\Data\ContentSyncCleanQueueItem;
use Drupal\druki_content\Queue\ContentSyncCleanQueueItemProcessor;
use Drupal\druki_content\Repository\ContentSyncQueueState;
use Drupal\druki_content\Repository\DrukiContentStorage;
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
  protected DrukiContentStorage $contentStorage;

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
   * Tests that processor works as expected.
   */
  public function testProcessor(): void {
    $existing_ids = $this->contentStorage->getQuery()
      ->accessCheck(FALSE)
      ->execute();
    // Save currently existed content IDs emulating that all created content
    // after this process will be consider as outdated and missing in source.
    $this->queueState->storeEntityIds($existing_ids);
    $outdated_content = $this->createDrukiContent();
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
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->processor = $this->container->get('druki_content.queue.clean_queue_processor');
    $this->contentStorage = $this->container->get('entity_type.manager')->getStorage('druki_content');
    $this->queueState = $this->container->get('druki_content.repository.content_sync_queue_state');
    $this->storeEntityIds(['druki_content']);
  }

}
