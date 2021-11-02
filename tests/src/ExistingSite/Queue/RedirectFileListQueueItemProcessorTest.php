<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Druki\Tests\Traits\EntityCleanupTrait;
use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\druki_redirect\Queue\RedirectFileListQueueItemProcessor;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for redirect file list queue item processor.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Queue\RedirectFileListQueueItemProcessor
 */
final class RedirectFileListQueueItemProcessorTest extends ExistingSiteBase {

  use SourceContentProviderTrait;
  use EntityCleanupTrait;

  /**
   * The queue item processor.
   */
  protected RedirectFileListQueueItemProcessor $processor;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->processor = $this->container->get('druki_redirect.queue.redirect_file_list_queue_item_processor');
    $this->storeEntityIds(['redirect']);
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Tests that processor works as expected.
   *
   * @todo Improve that test when RedirectRepository is created.
   */
  public function testProcessor(): void {
    $directory = $this->setupFakeSourceDir();

    $redirect_file = new RedirectFile($directory->url() . '/docs/ru/redirects.csv', 'ru');
    $file_list = new RedirectFileList();
    $file_list->addFile($redirect_file);
    $queue_item = new RedirectFileListQueueItem($file_list);

    $this->assertTrue($this->processor->isApplicable($queue_item));
    $this->processor->process($queue_item);
  }

}
