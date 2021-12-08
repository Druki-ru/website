<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\EventSubscriber;

use Drupal\druki_author\Builder\AuthorSyncQueueBuilderInterface;
use Drupal\druki_author\EventSubscriber\SourceContentEventSubscriber;
use Drupal\druki_content\Event\RequestSourceContentSyncEvent;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for source content event subscriber.
 *
 * @coversDefaultClass \Drupal\druki_author\EventSubscriber\SourceContentEventSubscriber
 */
final class AuthorSourceContentEventSubscriberTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests content sync request subscriber.
   */
  public function testOnSyncRequest(): void {
    $event = new RequestSourceContentSyncEvent('/foo/bar');
    $queue_builder = $this->buildAuthorSyncQueueBuilder();
    $subscriber = new SourceContentEventSubscriber($queue_builder);
    $subscriber->onSyncRequest($event);
  }

  /**
   * Builds a mock for author sync queue builder.
   *
   * @return \Drupal\druki_author\Builder\AuthorSyncQueueBuilderInterface
   *   A mock of author sync queue builder.
   */
  protected function buildAuthorSyncQueueBuilder(): AuthorSyncQueueBuilderInterface {
    $queue_builder = $this->prophesize(AuthorSyncQueueBuilderInterface::class);
    $queue_builder->buildFromDirectory(Argument::type('string'))->shouldBeCalled();
    return $queue_builder->reveal();
  }

  /**
   * Tests that subscribed to all required events.
   */
  public function testGetSubscribedEvents(): void {
    $subscribed_events = \array_keys(SourceContentEventSubscriber::getSubscribedEvents());
    $this->assertContains(RequestSourceContentSyncEvent::class, $subscribed_events);
  }

}
