<?php

declare(strict_types=1);

namespace Drupal\druki_author\EventSubscriber;

use Drupal\druki_author\Builder\AuthorSyncQueueBuilderInterface;
use Drupal\druki_content\Event\RequestSourceContentSyncEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides event subscriber for source content events.
 */
final class SourceContentEventSubscriber implements EventSubscriberInterface {

  /**
   * The queue builder.
   */
  protected AuthorSyncQueueBuilderInterface $queueBuilder;

  /**
   * Constructs a new SourceContentEventSubscriber object.
   *
   * @param \Drupal\druki_author\Builder\AuthorSyncQueueBuilderInterface $queue_builder
   *   The queue builder.
   */
  public function __construct(AuthorSyncQueueBuilderInterface $queue_builder) {
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
    $source_content_uri = $event->getSourceContentUri();
    $this->queueBuilder->buildFromDirectory("$source_content_uri/authors");
  }

}
