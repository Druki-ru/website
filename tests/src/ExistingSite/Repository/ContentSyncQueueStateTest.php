<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Repository;

use Drupal\druki_content\Repository\ContentSyncQueueState;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Tests content sync queue state storage.
 *
 * @coversDefaultClass \Drupal\druki_content\Repository\ContentSyncQueueState
 */
final class ContentSyncQueueStateTest extends ExistingSiteBase {

  /**
   * The content sync queue state.
   */
  protected ContentSyncQueueState $queueState;

  /**
   * {@inheritdoc}
   */
  public function tearDown() {
    $this->queueState->delete();
    parent::tearDown();
  }

  /**
   * Test that state storage works as expected.
   */
  public function testService(): void {
    $this->assertEmpty($this->queueState->getStoredEntityIds());
    $ids = [1, 2, 3];
    $this->queueState->storeEntityIds($ids);
    $this->assertEquals($ids, $this->queueState->getStoredEntityIds());
    $this->queueState->storeEntityIds([2, 3, 4]);
    $this->assertEquals([1, 2, 3, 4], $this->queueState->getStoredEntityIds());
    $this->queueState->delete();
    $this->assertEmpty($this->queueState->getStoredEntityIds());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->queueState = $this->container->get('druki_content.repository.content_sync_queue_state');
    $this->queueState->delete();
  }

}
