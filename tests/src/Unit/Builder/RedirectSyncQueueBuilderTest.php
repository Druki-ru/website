<?php

declare(strict_types=1);

namespace Druki\Test\Unit\Builder;

use Drupal\druki\Data\EntitySyncQueueItemListInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_redirect\Builder\RedirectSyncQueueBuilder;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Finder\RedirectFileFinderInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for redirect sync queue builder.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Builder\RedirectSyncQueueBuilder
 */
final class RedirectSyncQueueBuilderTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that building from directories works as expected.
   */
  public function testBuildFromDirectories(): void {
    $queue_manager = $this->buildQueueManager();
    $queue_builder = new RedirectSyncQueueBuilder($this->buildRedirectFileFinder(), $queue_manager);
    $queue_builder->buildFromDirectories(['foo/bar']);
  }

  /**
   * Builds mock of queue manager.
   *
   * @return \Drupal\druki\Queue\EntitySyncQueueManagerInterface
   *   The mock of queue manager.
   */
  public function buildQueueManager(): EntitySyncQueueManagerInterface {
    $queue_manager = $this->prophesize(EntitySyncQueueManagerInterface::class);
    $queue_manager->fillQueue(Argument::type(EntitySyncQueueItemListInterface::class))
      ->shouldBeCalled();
    return $queue_manager->reveal();
  }

  /**
   * Builds mock of redirect file finder.
   *
   * @return \Drupal\druki_redirect\Finder\RedirectFileFinderInterface
   *   The mock of redirect file finder.
   */
  public function buildRedirectFileFinder(): RedirectFileFinderInterface {
    $finder = $this->prophesize(RedirectFileFinderInterface::class);
    $finder->findAll(Argument::type('array'))->willReturn(new RedirectFileList());
    return $finder->reveal();
  }

}
