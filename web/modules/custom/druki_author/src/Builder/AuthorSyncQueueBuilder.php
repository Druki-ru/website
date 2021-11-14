<?php

declare(strict_types=1);

namespace Drupal\druki_author\Builder;

use Drupal\Core\Site\Settings;
use Drupal\druki\Data\EntitySyncQueueItemList;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_author\Data\AuthorCleanQueueItem;
use Drupal\druki_author\Data\AuthorList;
use Drupal\druki_author\Data\AuthorListQueueItem;
use Drupal\druki_author\Data\AuthorsFile;
use Drupal\druki_author\Finder\AuthorsFileFinderInterface;
use Drupal\druki_author\Parser\AuthorsFileParserInterface;

/**
 * Provides queue builder for authors synchronization.
 */
final class AuthorSyncQueueBuilder implements AuthorSyncQueueBuilderInterface {

  /**
   * The authors file finder.
   */
  protected AuthorsFileFinderInterface $finder;

  /**
   * The author sync queue manager.
   */
  protected EntitySyncQueueManagerInterface $queueManager;

  /**
   * The authors.json file parser.
   */
  protected AuthorsFileParserInterface $fileParser;

  /**
   * Constructs a new AuthorSyncQueueBuilder object.
   *
   * @param \Drupal\druki_author\Finder\AuthorsFileFinderInterface $finder
   *   The authors file finder.
   * @param \Drupal\druki_author\Parser\AuthorsFileParserInterface $file_parser
   *   The authors.json file parser.
   * @param \Drupal\druki\Queue\EntitySyncQueueManagerInterface $queue_manager
   *   The author sync queue manager.
   */
  public function __construct(AuthorsFileFinderInterface $finder, AuthorsFileParserInterface $file_parser, EntitySyncQueueManagerInterface $queue_manager) {
    $this->finder = $finder;
    $this->fileParser = $file_parser;
    $this->queueManager = $queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildFromDirectory(string $directory): void {
    $queue_items = new EntitySyncQueueItemList();

    $author_list = $this->finder->find($directory);
    if ($author_list) {
      $this->addAuthorList($author_list, $queue_items);
    }
    $queue_items->addQueueItem(new AuthorCleanQueueItem());

    $this->queueManager->fillQueue($queue_items);
  }

  /**
   * Adds author list into the queue.
   *
   * @param \Drupal\druki_author\Data\AuthorsFile $authors_file
   *   The authors file.
   * @param \Drupal\druki\Data\EntitySyncQueueItemList $queue_items
   *   The queue items.
   */
  protected function addAuthorList(AuthorsFile $authors_file, EntitySyncQueueItemList $queue_items): void {
    $author_list = $this->fileParser->parse($authors_file);
    if (!$author_list->getIterator()->count()) {
      return;
    }

    $chunk_size = Settings::get('entity_update_batch_size', 50);
    $authors_array = $author_list->getIterator()->getArrayCopy();
    $author_chunks = \array_chunk($authors_array, $chunk_size);
    foreach ($author_chunks as $author_chunk) {
      $author_list = new AuthorList();
      foreach ($author_chunk as $author) {
        $author_list->addAuthor($author);
      }
      $queue_item = new AuthorListQueueItem($author_list);
      $queue_items->addQueueItem($queue_item);
    }
  }

}
