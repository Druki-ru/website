<?php

declare(strict_types=1);

namespace Drupal\druki_author;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\druki_author\EventSubscriber\SourceContentEventSubscriber;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Provides dynamic services.
 *
 * @todo Refactor or remove this provider after refactor
 *    RequestSourceContentSyncEvent.
 */
final class DrukiAuthorServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container): void {
    // 'druki_author' module doesn't depends on 'druki_content', because of that
    // event subscriber registered only when 'druki_content' module is enabled.
    if ($container->has('druki_content.repository.content_source_settings')) {
      $source_content_event_subscriber = new Definition(SourceContentEventSubscriber::class);
      $source_content_event_subscriber->setArguments([
        new Reference('druki_content.repository.content_source_settings'),
        new Reference('druki_author.builder.author_sync_queue'),
      ]);
      $source_content_event_subscriber->addTag('event_subscriber');
      $container->addDefinitions([
        'druki_author.event_subscriber.content_source' => $source_content_event_subscriber,
      ]);
    }
  }

}
