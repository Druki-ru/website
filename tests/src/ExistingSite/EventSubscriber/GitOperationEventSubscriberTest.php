<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\EventSubscriber;

use Drupal\druki_git\Event\DrukiGitEvent;
use Drupal\druki_git\Git\GitInterface;
use Drupal\druki_redirect\EventSubscriber\GitOperationEventSubscriber;
use Drupal\druki_redirect\Queue\RedirectSyncQueueManagerInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for git operation event subscriber.
 *
 * @coversDefaultClass \Drupal\druki_redirect\EventSubscriber\GitOperationEventSubscriber
 */
final class GitOperationEventSubscriberTest extends ExistingSiteBase {

  use ProphecyTrait;

  /**
   * The event subscriber.
   */
  protected GitOperationEventSubscriber $subscriber;

  /**
   * Test that subscriber works as expected on Git pull event.
   */
  public function testOnPullFinish(): void {
    $subscriber = $this->container->get('druki_redirect.event_subscriber.git_opration');

    $git = $this->prophesize(GitInterface::class);
    $git->getRepositoryPath()->willReturn('foo/bar');
    $event = $this->prophesize(DrukiGitEvent::class);
    $event->git()->willReturn($git->reveal());

    $this->assertEmpty($this->queueManager->getDirectories());
    $subscriber->onPullFinish($event->reveal());
    $this->assertEquals(['foo/bar/docs'], $this->queueManager->getDirectories());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $queue_manager = new class() implements RedirectSyncQueueManagerInterface {
      protected array $directories = [];

      /**
       * @inheritDoc
       */
      public function buildFromDirectories(array $directories): void {
        $this->directories = $directories;
      }

      public function getDirectories(): array {
        return $this->directories;
      }

      /**
       * @inheritDoc
       */
      public function delete(): void {

      }

    };
    $this->container->set('druki_redirect.queue.sync_manager', $queue_manager);
    $this->queueManager = $this->container->get('druki_redirect.queue.sync_manager');
    $this->subscriber = $this->container->get('druki_redirect.event_subscriber.git_opration');
  }

}
