<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\Unit\Builder;

use Drupal\Core\Queue\QueueInterface;
use Drupal\druki\Data\EntitySyncQueueItemListInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_author\Builder\AuthorSyncQueueBuilder;
use Drupal\druki_author\Data\Author;
use Drupal\druki_author\Data\AuthorList;
use Drupal\druki_author\Data\AuthorsFile;
use Drupal\druki_author\Finder\AuthorsFileFinderInterface;
use Drupal\druki_author\Parser\AuthorsFileParserInterface;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for authors sync queue builder.
 *
 * @coversDefaultClass \Drupal\druki_author\Builder\AuthorSyncQueueBuilder
 */
final class AuthorsSyncQueueBuilderTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that building queue from directory works as expected.
   */
  public function testBuildFromDirectory(): void {
    $queue_manager = $this->buildQueueManager();
    $queue_builder = new AuthorSyncQueueBuilder(
      $this->buildAuthorsFileFinder(),
      $this->buildAuthorsFileParser(),
      $queue_manager,
    );

    $queue_builder->buildFromDirectory('/empty/dir');
    // Not found authors.json file still should have clean queue item.
    $this->assertEquals(1, $queue_manager->getQueue()->numberOfItems());

    $queue_builder->buildFromDirectory('/valid/file');
    $this->assertEquals(2, $queue_manager->getQueue()->numberOfItems());

    $queue_builder->buildFromDirectory('/empty/file');
    // Empty file behave the same as not existed file.
    $this->assertEquals(1, $queue_manager->getQueue()->numberOfItems());
  }

  /**
   * Builds mock of queue manager.
   *
   * @return \Drupal\druki\Queue\EntitySyncQueueManagerInterface
   *   The mock of queue manager.
   */
  protected function buildQueueManager(): EntitySyncQueueManagerInterface {
    $queue_items = [];

    $queue = $this->prophesize(QueueInterface::class);
    $queue->createItem(Argument::any())
      ->will(static function ($args) use (&$queue_items): void {
        $queue_items[] = $args[0];
      });
    $queue->deleteQueue()->will(static function () use (&$queue_items): void {
      $queue_items = [];
    });
    $queue->numberOfItems()->will(static function () use (&$queue_items) {
      return \count($queue_items);
    });

    $queue_manager = $this->prophesize(EntitySyncQueueManagerInterface::class);
    $queue_manager->fillQueue(Argument::type(EntitySyncQueueItemListInterface::class))
      ->will(static function ($args) use (&$queue_items): void {
        $queue_items = [];
        foreach ($args[0] as $queue_item) {
          $queue_items[] = $queue_item;
        }
      });
    $queue_manager->getQueue()->willReturn($queue->reveal());
    return $queue_manager->reveal();
  }

  /**
   * Builds mock of authors file finder.
   *
   * @return \Drupal\druki_author\Finder\AuthorsFileFinderInterface
   *   The mock of authors file finder.
   */
  protected function buildAuthorsFileFinder(): AuthorsFileFinderInterface {
    vfsStream::setup();
    vfsStream::create([
      'authors' => [
        'authors.json' => '',
      ],
    ]);

    $finder = $this->prophesize(AuthorsFileFinderInterface::class);
    $finder->find('/empty/dir')->willReturn(NULL);
    $authors_file = new AuthorsFile(vfsStream::url('root/authors/authors.json'));
    $finder->find('/valid/file')->willReturn($authors_file);
    $finder->find('/empty/file')->willReturn($authors_file);
    return $finder->reveal();
  }

  /**
   * Builds mock of authors file parser.
   *
   * @return \Drupal\druki_author\Parser\AuthorsFileParserInterface
   *   The mock of authors file parser.
   */
  protected function buildAuthorsFileParser(): AuthorsFileParserInterface {
    $parser = $this->prophesize(AuthorsFileParserInterface::class);
    $authors = new AuthorList();
    $authors->addAuthor(new Author());
    $parser->parse(Argument::any())->will(static function () use ($authors, $parser) {
      // On second call return empty result.
      $parser->parse(Argument::any())->willReturn(new AuthorList());
      return $authors;
    });
    return $parser->reveal();
  }

}
