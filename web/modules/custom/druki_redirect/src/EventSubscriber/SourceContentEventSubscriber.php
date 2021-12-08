<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\EventSubscriber;

use Drupal\druki_content\Event\RequestSourceContentSyncEvent;
use Drupal\druki_redirect\Builder\RedirectSyncQueueBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides event subscriber for source content events.
 */
final class SourceContentEventSubscriber implements EventSubscriberInterface {

  /**
   * The queue builder.
   */
  protected RedirectSyncQueueBuilderInterface $queueBuilder;

  /**
   * Constructs a new SourceContentEventSubscriber object.
   *
   * @param \Drupal\druki_redirect\Builder\RedirectSyncQueueBuilderInterface $queue_builder
   *   The queue builder.
   */
  public function __construct(RedirectSyncQueueBuilderInterface $queue_builder) {
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
    $repository_path = $event->getSourceContentUri();
    $directories = [
      // Only documents expected to have redirects.
      "$repository_path/docs",
    ];
    $this->queueBuilder->buildFromDirectories($directories);
  }

}
