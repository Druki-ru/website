<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Druki\Tests\Traits\EntityCleanupTrait;
use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_redirect\Data\Redirect;
use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\druki_redirect\Queue\RedirectFileListQueueItemProcessor;
use Drupal\druki_redirect\Repository\RedirectRepositoryInterface;
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
   * The redirect repository.
   */
  protected RedirectRepositoryInterface $redirectRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->processor = $this->container->get('druki_redirect.queue.redirect_file_list_queue_item_processor');
    $this->redirectRepository = $this->container->get('druki_redirect.repository.redirect');
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
   */
  public function testProcessor(): void {
    $directory = $this->setupFakeSourceDir();

    $redirect_file = new RedirectFile($directory->url() . '/docs/ru/redirects.csv', 'ru');
    $file_list = new RedirectFileList();
    $file_list->addFile($redirect_file);
    $queue_item = new RedirectFileListQueueItem($file_list);

    $handle = \fopen($redirect_file->getPathname(), 'r');
    $row = \fgetcsv($handle);
    // Get the first row from redirectcs.csv to do assertions.
    $redirect = Redirect::createFromUserInput($row[0], $row[1]);
    \fclose($handle);

    $this->assertNull($this->redirectRepository->findRedirect($redirect, $redirect_file->getLanguage()));
    $this->assertTrue($this->processor->isApplicable($queue_item));
    $this->processor->process($queue_item);
    $this->assertIsInt($this->redirectRepository->findRedirect($redirect, $redirect_file->getLanguage()));
    $redirect_entity = $this->redirectRepository->loadRedirect($redirect, $redirect_file->getLanguage());

    $directory = $this->setupFakeSourceDirUpdate();
    $redirect_file = new RedirectFile($directory->url() . '/docs/ru/redirects.csv', 'ru');
    $file_list = new RedirectFileList();
    $file_list->addFile($redirect_file);
    $queue_item = new RedirectFileListQueueItem($file_list);

    $handle = \fopen($redirect_file->getPathname(), 'r');
    $row = \fgetcsv($handle);
    // Get the first row from redirectcs.csv to do assertions.
    $redirect = Redirect::createFromUserInput($row[0], $row[1]);
    \fclose($handle);

    // The source path is the same for redirect, it should be found now.
    $this->assertIsInt($this->redirectRepository->findRedirect($redirect, $redirect_file->getLanguage()));
    $redirect_entity_2 = $this->redirectRepository->loadRedirect($redirect, $redirect_file->getLanguage());
    // But the redirect is changed, so it should be updated after processing.
    // Make sure it's the same before processing first.
    $this->assertEquals($redirect_entity->getRedirect(), $redirect_entity_2->getRedirect());
    $this->assertTrue($this->processor->isApplicable($queue_item));
    $this->processor->process($queue_item);
    $redirect_entity_2 = $this->redirectRepository->loadRedirect($redirect, $redirect_file->getLanguage());
    $this->assertNotEquals($redirect_entity->getRedirect(), $redirect_entity_2->getRedirect());
  }

}
