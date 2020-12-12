<?php

namespace Druki\Tests\Unit\Cron;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\DependencyInjection\Container;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\druki\Cron\CheckDrupalReleasesCron;
use Drupal\druki\Drupal\DrupalProjectsInterface;
use Drupal\druki\Drupal\DrupalReleasesInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
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
    $time = $this->buildTimeService(979516800);
    $cache_tags_invalidator = $this->buildCacheTagsInvalidator();
    $drupal_releases = $this->buildDrukiDrupalReleases([
      'expires' => 980000000,
    ]);
    $drupal_projects = $this->buildDrukiDrupalProjects();

    $drupal_releases_cron = new CheckDrupalReleasesCron($time, $cache_tags_invalidator, $drupal_releases, $drupal_projects);
    $drupal_releases_cron->process();

    // Nothing must be changed if not expired still.
    $this->assertEquals([
      'expires' => 980000000,
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
   * @return \Drupal\druki\Drupal\DrupalReleasesInterface
   */
  protected function buildDrukiDrupalReleases(array $initial_values): DrupalReleasesInterface {
    $drupal_releases = $this->prophesize(DrupalReleasesInterface::class);
    $drupal_releases->get()->willReturn($initial_values);
    $drupal_releases->set(Argument::type('array'))->will(function ($args) use ($drupal_releases): void {
      $drupal_releases->get()->willReturn($args[0]);
    });
    return $drupal_releases->reveal();
  }

  /**
   * Builds mock for 'druki.drupal_projects' service.
   *
   * @param array|null $stable_version
   *   The stable version info.
   * @param array|null $minor_version
   *   The minor version info.
   *
   * @return \Drupal\druki\Drupal\DrupalProjectsInterface
   */
  protected function buildDrukiDrupalProjects(?array $stable_version = NULL, ?array $minor_version = NULL): DrupalProjectsInterface {
    $drupal_projects = $this->prophesize(DrupalProjectsInterface::class);
    $drupal_projects->getCoreLastStableVersion()->willReturn($stable_version);
    $drupal_projects->getCoreLastMinorVersion()->willReturn($minor_version);
    return $drupal_projects->reveal();
  }

  /**
   * Test case when stable or minor version not retrieved properly.
   *
   * @covers ::process
   */
  public function testProblemWithGettingVersion(): void {
    $time = $this->buildTimeService(979516800);
    $cache_tags_invalidator = $this->buildCacheTagsInvalidator();
    $drupal_releases = $this->buildDrukiDrupalReleases([
      'expires' => 970000000,
    ]);
    $drupal_projects = $this->buildDrukiDrupalProjects();

    $drupal_releases_cron = new CheckDrupalReleasesCron($time, $cache_tags_invalidator, $drupal_releases, $drupal_projects);
    $drupal_releases_cron->process();

    // Nothing must be changed if at least one release is NULL.
    $this->assertEquals([
      'expires' => 970000000,
    ], $drupal_releases->get());
  }

  /**
   * Test case when release information was updated.
   *
   * @covers ::process
   */
  public function testReleaseUpdated(): void {
    $time = $this->buildTimeService(979516800);
    $cache_tags_invalidator = $this->buildCacheTagsInvalidator();
    $drupal_releases = $this->buildDrukiDrupalReleases([
      'expires' => 970000000,
      'last_stable_release' => [
        'tag' => '9.0.0',
      ],
      'last_minor_release' => [
        'tag' => '9.0.0',
      ],
    ]);
    $new_stable_release = [
      'tag' => '9.0.1',
    ];
    $new_minor_release = [
      'tag' => '9.1.0',
    ];
    $drupal_projects = $this->buildDrukiDrupalProjects($new_stable_release, $new_minor_release);

    $drupal_releases_cron = new CheckDrupalReleasesCron($time, $cache_tags_invalidator, $drupal_releases, $drupal_projects);
    $drupal_releases_cron->process();

    $this->assertEquals([
      'expires' => $this->calculateExpires($time->getRequestTime()),
      'last_stable_release' => $new_stable_release,
      'last_minor_release' => $new_minor_release,
    ], $drupal_releases->get());
  }

  /**
   * Calculates expected expires.
   *
   * @param int $request_time
   *   The request time to count from.
   *
   * @return int
   *   The timestamp of expires time.
   */
  protected function calculateExpires(int $request_time): int {
    $request_datetime = DrupalDateTime::createFromTimestamp($request_time);
    $next_wednesday = new DrupalDateTime('next wednesday');
    $next_wednesday_interval = $request_datetime->diff($next_wednesday);
    if ($next_wednesday_interval->d == 6) {
      return $request_time + (60 * 60 * 1);
    }
    elseif ($next_wednesday_interval->d == 0) {
      return $next_wednesday->getTimestamp();
    }
    else {
      return $request_time + (60 * 60 * 24);
    }
  }

  /**
   * {@inhereitdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $language = $this->prophesize(LanguageInterface::class);
    $language->getId()->willReturn('en');

    $language_manager = $this->prophesize(LanguageManagerInterface::class);
    $language_manager->getCurrentLanguage()->willReturn($language->reveal());

    $container = new Container();
    $container->set('language_manager', $language_manager->reveal());
    \Drupal::setContainer($container);
  }

}
