<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\EventSubscriber;

use Drupal\druki_git\Event\DrukiGitEvent;
use Drupal\druki_git\Event\DrukiGitEvents;
use Drupal\druki_redirect\Queue\RedirectSyncQueueManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides event subscribers for Git operations.
 */
final class GitOperationEventSubscriber implements EventSubscriberInterface {

  /**
   * The redirect queue manager.
   */
  protected RedirectSyncQueueManagerInterface $redirectQueueManager;

  /**
   * Constructs a new GitOperationEventSubscriber object.
   *
   * @param \Drupal\druki_redirect\Queue\RedirectSyncQueueManagerInterface $redirect_queue_manager
   *   The redirect queue manager.
   */
  public function __construct(RedirectSyncQueueManagerInterface $redirect_queue_manager) {
    $this->redirectQueueManager = $redirect_queue_manager;
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
    $repository_path = $event->git()->getRepositoryPath();
    $directories = [
      // Only documents expected to have redirects.
      "$repository_path/docs",
    ];
    $this->redirectQueueManager->buildFromDirectories($directories);
  }

}
