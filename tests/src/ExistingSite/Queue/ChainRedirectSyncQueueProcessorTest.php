<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;
use Drupal\druki_redirect\Queue\ChainRedirectSyncQueueProcessor;
use Drupal\druki_redirect\Queue\RedirectSyncQueueItemProcessorInterface;
use Drupal\druki_redirect\Repository\RedirectSyncQueueState;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for chain redirect sync queue processor.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Queue\ChainRedirectSyncQueueProcessor
 */
final class ChainRedirectSyncQueueProcessorTest extends ExistingSiteBase {

  use ProphecyTrait;

  /**
   * The chain redirect sync queue processor.
   */
  protected ChainRedirectSyncQueueProcessor $chainProcessor;

  /**
   * The redirect sync queue state.
   */
  protected EntitySyncQueueStateInterface $syncState;

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->syncState->delete();
    parent::tearDown();
  }

  /**
   * Tests that processor works as expected.
   */
  public function testProcessor(): void {
    $queue_item_1 = $this->prophesize(EntitySyncQueueItemInterface::class);
    $queue_item_1->getPayload()->willReturn('First!');

    $queue_item_2 = $this->prophesize(EntitySyncQueueItemInterface::class);
    $queue_item_2->getPayload()->willReturn('Second!');

    $processor_1 = $this->prophesize(RedirectSyncQueueItemProcessorInterface::class);
    $processor_1->isApplicable(Argument::any())->will(function ($args) {
      return $args['0']->getPayload() == 'First!';
    });
    $processor_1->process(Argument::any())->willReturn([10]);

    $processor_2 = $this->prophesize(RedirectSyncQueueItemProcessorInterface::class);
    $processor_2->isApplicable(Argument::any())->will(function ($args) {
      return $args['0']->getPayload() == 'Second!';
    });
    $processor_2->process(Argument::any())->willReturn([20]);

    $this->chainProcessor->addProcessor($processor_1->reveal());
    $this->chainProcessor->addProcessor($processor_2->reveal());
    $this->assertTrue($this->chainProcessor->isApplicable($queue_item_1->reveal()));
    $this->assertTrue($this->chainProcessor->isApplicable($queue_item_2->reveal()));

    $this->assertEmpty($this->syncState->getEntityIds());
    $this->chainProcessor->process($queue_item_1->reveal());
    $this->assertEquals([10], $this->syncState->getEntityIds());
    $this->chainProcessor->process($queue_item_2->reveal());
    $this->assertEquals([10, 20], $this->syncState->getEntityIds());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->chainProcessor = $this->container->get('druki_redirect.queue.chain_sync_processor');
    $this->syncState = $this->container->get('druki_redirect.repository.redirect_sync_queue_state');
    $this->syncState->delete();
  }

}
