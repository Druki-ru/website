<?php

namespace Drupal\Tests\druki\Aggregator;

use Drupal\druki\Aggregator\DrupalCoreVersionAggregator;
use Drupal\Tests\UnitTestCase;
use Drupal\update\UpdateFetcherInterface;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Test druki.aggregator.drupal_core_version service.
 *
 * @coversDefaultClass \Drupal\druki\Aggregator\DrupalCoreVersionAggregator
 */
class DrupalCoreVersionAggregatorTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * A drupal projects service.
   */
  protected DrupalCoreVersionAggregator $drupalProjects;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->drupalProjects = new DrupalCoreVersionAggregator($this->buildUpdateFetcher());
  }

  /**
   * Builds mock for UpdateFetcherInterface.
   *
   * @return \Drupal\update\UpdateFetcherInterface
   *   The update fetcher.
   */
  protected function buildUpdateFetcher(): UpdateFetcherInterface {
    $response_contents = \file_get_contents(__DIR__ . '/../../../fixtures/drupal-release-history.xml');

    $update_fetcher = $this->prophesize(UpdateFetcherInterface::class);
    $update_fetcher->fetchProjectData(['name' => 'drupal'])
      ->willReturn($response_contents);

    return $update_fetcher->reveal();
  }

  /**
   * Test getting project last stable release.
   *
   * @covers ::getCoreLastStableVersion
   */
  public function testGetProjectLastStableRelease(): void {
    $actual = $this->drupalProjects->getCoreLastStableVersion();
    $this->assertSame('8.8.4', $actual['version']);
    // The stable release is always published.
    $this->assertSame($actual['status'], 'published');
  }

  /**
   * Test getting project last minor version.
   *
   * @covers ::getCoreLastMinorVersion
   */
  public function testGetCoreLastMinorVersion(): void {
    $actual = $this->drupalProjects->getCoreLastMinorVersion();
    // The last minor version is always with patch level 0.
    $this->assertSame('8.8.0', $actual['version']);
  }

}
