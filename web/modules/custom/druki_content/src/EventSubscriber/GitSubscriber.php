<?php

namespace Drupal\druki_content\EventSubscriber;

use Drupal\druki_content\Sync\SyncQueueManager;
use Drupal\druki_git\Event\DrukiGitEvent;
use Drupal\druki_git\Event\DrukiGitEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides git events subscriber.
 */
final class GitSubscriber implements EventSubscriberInterface {

  /**
   * The sync queue manager.
   *
   * @var \Drupal\druki_content\Sync\SyncQueueManager
   */
  protected $queueManager;

  /**
   * Constructs a new GitSubscriber object.
   *
   * @param \Drupal\druki_content\Sync\SyncQueueManager $sync_queue_manager
   *   The sync queue manager.
   */
  public function __construct(SyncQueueManager $sync_queue_manager) {
    $this->queueManager = $sync_queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      DrukiGitEvents::FINISH_PULL => ['onPullFinish'],
    ];
  }

  /**
   * Reacts on successful pull.
   *
   * @param \Drupal\druki_git\Event\DrukiGitEvent $event
   *   The git event.
   */
  public function onPullFinish(DrukiGitEvent $event): void {
    $this->queueManager->buildFromPath($event->git()->getRepositoryPath());
  }

}
