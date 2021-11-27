<?php

declare(strict_types=1);

namespace Drupal\druki_author\EventSubscriber;

use Drupal\druki_author\Builder\AuthorSyncQueueBuilderInterface;
use Drupal\druki_content\Event\RequestSourceContentSyncEvent;
use Drupal\druki_content\Repository\ContentSourceSettingsInterface;
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
  protected AuthorSyncQueueBuilderInterface $queueBuilder;

  /**
   * Constructs a new SourceContentEventSubscriber object.
   *
   * @param \Drupal\druki_content\Repository\ContentSourceSettingsInterface $content_source_settings
   *   The content source settings repository.
   * @param \Drupal\druki_author\Builder\AuthorSyncQueueBuilderInterface $queue_builder
   *   The queue builder.
   */
  public function __construct(ContentSourceSettingsInterface $content_source_settings, AuthorSyncQueueBuilderInterface $queue_builder) {
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
    $this->queueBuilder->buildFromDirectory("$repository_path/author");
  }

}
