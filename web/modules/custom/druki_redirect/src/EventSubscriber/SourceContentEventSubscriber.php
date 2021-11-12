<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\EventSubscriber;

use Drupal\druki_content\Event\RequestSourceContentSyncEvent;
use Drupal\druki_content\Repository\ContentSourceSettingsInterface;
use Drupal\druki_redirect\Builder\RedirectSyncQueueBuilderInterface;
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
   * The queue builder.
   */
  protected RedirectSyncQueueBuilderInterface $queueBuilder;

  /**
   * Constructs a new SourceContentEventSubscriber object.
   *
   * @param \Drupal\druki_content\Repository\ContentSourceSettingsInterface $content_source_settings
   *   The content source settings repository.
   * @param \Drupal\druki_redirect\Builder\RedirectSyncQueueBuilderInterface $queue_builder
   *   The queue builder.
   */
  public function __construct(ContentSourceSettingsInterface $content_source_settings, RedirectSyncQueueBuilderInterface $queue_builder) {
    $this->contentSourceSettings = $content_source_settings;
    $this->queueBuilder = $queue_builder;
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
    $this->queueBuilder->buildFromDirectories($directories);
  }

}
