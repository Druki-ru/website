<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_redirect\ExistingSite\Queue;

use Drupal\druki_redirect\Data\Redirect;
use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\druki_redirect\Queue\RedirectFileListQueueItemProcessor;
use Drupal\druki_redirect\Repository\RedirectRepositoryInterface;
use Drupal\Tests\druki\Traits\EntityCleanupTrait;
use org\bovigo\vfs\vfsStream;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for redirect file list queue item processor.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Queue\RedirectFileListQueueItemProcessor
 */
final class RedirectFileListQueueItemProcessorTest extends ExistingSiteBase {

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
   * Tests that processor works as expected.
   */
  public function testProcessor(): void {
    $redirects_content = <<<'EOF'
    /foo-bar,/
    /foo-bar?with=query,/
    /foo-baz,/#fragment
    EOF;

    $redirects_content_update = <<<'EOF'
    /foo-bar,/#fragment
    /foo-bar?with=query,/?with=another-query
    /foo-baz,/?also=with-query#fragment
    EOF;

    vfsStream::setup();
    vfsStream::create([
      'redirects.csv' => $redirects_content,
      'redirects-updated.csv' => $redirects_content_update,
    ]);

    $redirect_file = new RedirectFile(vfsStream::url('root/redirects.csv'), 'ru');
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

    $redirect_file = new RedirectFile(vfsStream::url('root/redirects-updated.csv'), 'ru');
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

}
