<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Druki\Tests\Traits\EntityCleanupTrait;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;
use Drupal\druki_redirect\Data\RedirectCleanQueueItem;
use Drupal\druki_redirect\Queue\ChainRedirectSyncQueueProcessorInterface;
use Drupal\druki_redirect\Queue\RedirectCleanQueueItemProcessor;
use Drupal\druki_redirect\Repository\RedirectSyncQueueState;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for redirect sync queue cleanup processor.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Queue\RedirectCleanQueueItemProcessor
 */
final class RedirectCleanQueueItemProcessorTest extends ExistingSiteBase {

  use EntityCleanupTrait;

  /**
   * Provides redirect sync clean queue processor.
   */
  protected RedirectCleanQueueItemProcessor $processor;

  /**
   * The redirect storage.
   */
  protected ContentEntityStorageInterface $redirectStorage;

  /**
   * The redirect sync queue state.
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
    $existing_ids = $this->redirectStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('druki_redirect', TRUE)
      ->execute();
    $this->queueState->storeEntityIds($existing_ids);
    $outdated_redirect = $this->redirectStorage->create();
    $outdated_redirect->set('druki_redirect', TRUE);
    $outdated_redirect->save();
    $outdated_redirect_id = $outdated_redirect->id();

    $count = $this->redirectStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('rid', $outdated_redirect_id)
      ->count()
      ->execute();
    $this->assertEquals(1, $count);

    $queue_item = new RedirectCleanQueueItem();
    $this->processor->process($queue_item);

    $count = $this->redirectStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('rid', $outdated_redirect_id)
      ->count()
      ->execute();
    $this->assertEquals(0, $count);

    $new_ids = $this->redirectStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('druki_redirect', TRUE)
      ->execute();
    $this->assertSame($existing_ids, $new_ids);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->processor = $this->container->get('druki_redirect.queue.redirect_clean_queue_item_processor');
    $this->redirectStorage = $this->container->get('entity_type.manager')->getStorage('redirect');
    $this->queueState = $this->container->get('druki_redirect.repository.redirect_sync_queue_state');
    $this->storeEntityIds(['redirect']);
  }

}
