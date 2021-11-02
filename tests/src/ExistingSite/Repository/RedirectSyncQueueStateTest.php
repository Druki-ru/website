<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Repository;

use Drupal\druki_redirect\Repository\RedirectSyncQueueState;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Tests redirect sync queue state storage.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Repository\RedirectSyncQueueState
 */
final class RedirectSyncQueueStateTest extends ExistingSiteBase {

  /**
   * The content sync queue state.
   */
  protected RedirectSyncQueueState $queueState;

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
    $this->queueState = $this->container->get('druki_redirect.repository.redirect_sync_queue_state');
    $this->queueState->delete();
  }

}
