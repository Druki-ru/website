<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\EventSubscriber;

use Drupal\druki_content\Event\RequestSourceContentSyncEvent;
use Drupal\druki_content\Repository\ContentSourceSettingsInterface;
use Drupal\druki_redirect\Queue\RedirectSyncQueueManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides event subscriber for source content events.
 */
final class SourceContentEventSubscriber implements EventSubscriberInterface {

  /**
   * The content source settings repository.
   */
  protected ContentSourceSettingsInterface $contentSourceSettings;

  /**
   * The redirect sync queue manager.
   */
  protected RedirectSyncQueueManagerInterface $redirectSyncQueueManager;

  /**
   * Constructs a new SourceContentEventSubscriber object.
   *
   * @param \Drupal\druki_content\Repository\ContentSourceSettingsInterface $content_source_settings
   *   The content source settings repository.
   * @param \Drupal\druki_redirect\Queue\RedirectSyncQueueManagerInterface $redirect_sync_queue_manager
   *   The redurect sync queue manager.
   */
  public function __construct(ContentSourceSettingsInterface $content_source_settings, RedirectSyncQueueManagerInterface $redirect_sync_queue_manager) {
    $this->contentSourceSettings = $content_source_settings;
    $this->redirectSyncQueueManager = $redirect_sync_queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      RequestSourceContentSyncEvent::class => ['onSyncRequest'],
    ];
  }

  /**
   * Reacts on request for the source content synchronization.
   *
   * @param \Drupal\druki_content\Event\RequestSourceContentSyncEvent $event
   *   The event instance.
   */
  public function onSyncRequest(RequestSourceContentSyncEvent $event): void {
    $repository_path = $this->contentSourceSettings->getRepositoryUri();
    $directories = [
      // Only documents expected to have redirects.
      "$repository_path/docs",
    ];
    $this->redirectSyncQueueManager->buildFromDirectories($directories);
  }

}
