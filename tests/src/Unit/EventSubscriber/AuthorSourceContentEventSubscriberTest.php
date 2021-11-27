<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\EventSubscriber;

use Drupal\druki_author\Builder\AuthorSyncQueueBuilderInterface;
use Drupal\druki_author\EventSubscriber\SourceContentEventSubscriber;
use Drupal\druki_content\Repository\ContentSourceSettingsInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for source content event subscriber.
 *
 * @coversDefaultClass \Drupal\druki_author\EventSubscriber\SourceContentEventSubscriber
 */
final class AuthorSourceContentEventSubscriberTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests content sync request subscriber.
   */
  public function testOnSyncRequest(): void {
    $settings = $this->buildContentSourceSettings();
    $queue_builder = $this->buildAuthorSyncQueueBuilder();
    $subscriber = new SourceContentEventSubscriber($settings, $queue_builder);
    $this->markTestIncomplete('Complete after refactoring RequestSourceContentSyncEvent');
  }

  /**
   * Builds a mock for content source settings.
   *
   * @return \Drupal\druki_content\Repository\ContentSourceSettingsInterface
   *   A mock of content source settings.
   */
  protected function buildContentSourceSettings(): ContentSourceSettingsInterface {
    $settings = $this->prophesize(ContentSourceSettingsInterface::class);

    return $settings->reveal();
  }

  /**
   * Builds a mock for author sync queue builder.
   *
   * @return \Drupal\druki_author\Builder\AuthorSyncQueueBuilderInterface
   *   A mock of author sync queue builder.
   */
  protected function buildAuthorSyncQueueBuilder(): AuthorSyncQueueBuilderInterface {
    $queue_builder = $this->prophesize(AuthorSyncQueueBuilderInterface::class);

    return $queue_builder->reveal();
  }

}
