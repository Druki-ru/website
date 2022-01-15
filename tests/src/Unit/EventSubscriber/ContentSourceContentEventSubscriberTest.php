<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\EventSubscriber;

use Drupal\druki\Process\GitInterface;
use Drupal\druki_content\Builder\ContentSyncQueueBuilderInterface;
use Drupal\druki_content\Event\ContentSourceSyncRequestEvent;
use Drupal\druki_content\Event\ContentSourceUpdateRequestEvent;
use Drupal\druki_content\EventSubscriber\ContentSourceEventSubscriber;
use Drupal\druki_content\Repository\ContentSourceSettingsInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Process\Process;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Provides test for source content events subscriber.
 *
 * @coversDefaultClass \Drupal\druki_content\EventSubscriber\ContentSourceEventSubscriber
 */
final class ContentSourceContentEventSubscriberTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Store dispatched events.
   */
  protected array $dispatchedEvents = [];

  /**
   * Indicates is queue build was called.
   */
  protected bool $isSyncQueueBuilt = FALSE;

  /**
   * Test that update request subscriber works as expected.
   */
  public function testOnUpdateRequest(): void {
    $event_subscriber = $this->buildEventSubscriber();

    $this->assertArrayHasKey(ContentSourceUpdateRequestEvent::class, ContentSourceEventSubscriber::getSubscribedEvents());
    $event = new ContentSourceUpdateRequestEvent();
    // First call should fail on git pull and doesn't call dispatcher for sync.
    $event_subscriber->onUpdateRequest($event);
    $this->assertEmpty($this->dispatchedEvents);

    $event_subscriber->onUpdateRequest($event);
    $this->assertContains(ContentSourceSyncRequestEvent::class, $this->dispatchedEvents);
  }

  /**
   * Builds event subscriber with mocked dependencies.
   *
   * @return \Drupal\druki_content\EventSubscriber\ContentSourceEventSubscriber
   *   The subscriber instance.
   */
  protected function buildEventSubscriber(): ContentSourceEventSubscriber {
    return new ContentSourceEventSubscriber(
      $this->buildContentSourceSettings(),
      $this->buildContentSyncQueueBuilder(),
      $this->buildGit(),
      $this->buildEventDispatcher(),
    );
  }

  /**
   * Builds content source settings mock.
   *
   * @return \Drupal\druki_content\Repository\ContentSourceSettingsInterface
   *   The mock instance.
   */
  protected function buildContentSourceSettings(): ContentSourceSettingsInterface {
    $source_settings = $this->prophesize(ContentSourceSettingsInterface::class);
    $source_settings->getRepositoryUri()->willReturn('/foo/bar');
    return $source_settings->reveal();
  }

  /**
   * Builds content sync queue manager mock.
   *
   * @return \Drupal\druki_content\Builder\ContentSyncQueueBuilderInterface
   *   The mock instance.
   */
  protected function buildContentSyncQueueBuilder(): ContentSyncQueueBuilderInterface {
    $this->isSyncQueueBuilt = FALSE;
    $self = $this;
    $queue_manager = $this->prophesize(ContentSyncQueueBuilderInterface::class);
    $queue_manager->buildFromPath(Argument::any())->will(function () use ($self) {
      $self->isSyncQueueBuilt = TRUE;
    });
    return $queue_manager->reveal();
  }

  /**
   * Builds git process mock.
   *
   * @return \Drupal\druki\Process\GitInterface
   *   The mock instance.
   */
  protected function buildGit(): GitInterface {
    $process = $this->prophesize(Process::class);
    $process->run()->willReturn(1);
    // First call will fail.
    $process->isSuccessful()->will(function () use ($process) {
      $process->isSuccessful()->willReturn(TRUE);
      return FALSE;
    });
    $git = $this->prophesize(GitInterface::class);
    $git->pull(Argument::any())->willReturn($process->reveal());
    return $git->reveal();
  }

  /**
   * Builds event dispatcher mock.
   *
   * @return \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
   *   The mock instance.
   */
  protected function buildEventDispatcher(): EventDispatcherInterface {
    $this->dispatchedEvents = [];
    $self = $this;
    $event_dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $event_dispatcher->dispatch(Argument::any())->will(function ($args) use ($self) {
      $self->dispatchedEvents[] = $args[0]::class;
    });
    return $event_dispatcher->reveal();
  }

  /**
   * Tests that sync request works as expected.
   */
  public function testOnSyncRequest(): void {
    $event_subscriber = $this->buildEventSubscriber();

    $this->assertArrayHasKey(ContentSourceSyncRequestEvent::class, ContentSourceEventSubscriber::getSubscribedEvents());
    $event = new ContentSourceSyncRequestEvent('foo/bar');
    $this->assertFalse($this->isSyncQueueBuilt);
    $event_subscriber->onSyncRequest($event);
    $this->assertTrue($this->isSyncQueueBuilt);
  }

}
