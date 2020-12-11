<?php

namespace Drupal\Tests\Unit\Cron;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\druki\Cron\CheckDrupalReleasesCron;
use Drupal\druki\Drupal\DrupalProjects;
use Drupal\druki\Drupal\DrupalReleases;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for service druki.cron.check_drupal_releases.
 *
 * @coversDefaultClass \Drupal\druki\Cron\CheckDrupalReleasesCron
 */
final class CheckDrupalReleasesCronTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Test case when expires is not pass yet.
   *
   * @covers ::process
   */
  public function testDataStillValid(): void {
    $time = $this->buildTimeService(900);
    $cache_tags_invalidator = $this->buildCacheTagsInvalidator();
    $drupal_releases = $this->buildDrukiDrupalReleases([
      'expires' => 1000,
    ]);
    $drupal_projects = $this->buildDrukiDrupalProjects();

    $drupalReleasesCron = new CheckDrupalReleasesCron($time, $cache_tags_invalidator, $drupal_releases, $drupal_projects);
    $drupalReleasesCron->process();

    // Nothing must be changed if not expired still.
    $this->assertEquals([
      'expires' => 1000,
    ], $drupal_releases->get());
  }

  /**
   * Builds mock for 'datetime.time' service.
   *
   * @param int $request_time
   *   The request time returned.
   *
   * @return \Drupal\Component\Datetime\TimeInterface
   */
  protected function buildTimeService(int $request_time): TimeInterface {
    $time = $this->prophesize(TimeInterface::class);
    $time->getRequestTime()->willReturn($request_time);

    return $time->reveal();
  }

  /**
   * Builds mock for 'cache_tags.invalidator' service.
   *
   * @return \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected function buildCacheTagsInvalidator(): CacheTagsInvalidatorInterface {
    $cache_tags_invalidator = $this->prophesize(CacheTagsInvalidatorInterface::class);
    return $cache_tags_invalidator->reveal();
  }

  /**
   * Builds mock for 'druki.drupal_releases' service.
   *
   * @param array $inital_values
   *   The storage initial values.
   *
   * @return \Drupal\druki\Drupal\DrupalReleases
   */
  protected function buildDrukiDrupalReleases(array $initial_values): DrupalReleases {
    $drupal_releases = $this->prophesize(DrupalReleases::class);
    $drupal_releases->get()->willReturn($initial_values);
    // @todo Save set values.
    return $drupal_releases->reveal();
  }

  /**
   * Builds mock for 'druki.drupal_projects' service.
   *
   * @return \Drupal\druki\Drupal\DrupalProjects
   */
  protected function buildDrukiDrupalProjects(): DrupalProjects {
    $drupal_projects = $this->prophesize(DrupalProjects::class);
    return $drupal_projects->reveal();
  }

}
