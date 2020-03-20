<?php

namespace Drupal\Tests\druki\Unit\Service;

use Drupal\druki\Service\DrupalProjects;
use Drupal\Tests\UnitTestCase;
use Drupal\update\UpdateFetcherInterface;

/**
 * Test druki.drupal_projects service.
 *
 * @coversDefaultClass \Drupal\druki\Service\DrupalProjects
 */
class DrupalProjectsTest extends UnitTestCase {

  /**
   * The drupal projects service.
   *
   * @var \Drupal\druki\Service\DrupalProjects
   */
  protected $drupalProjects;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $update_fetcher = $this->createMock(UpdateFetcherInterface::class);
    $update_fetcher
      ->method('fetchProjectData')
      ->willReturn(file_get_contents(__DIR__ . '/../../../fixtures/drupal-release-history.xml'));
    $this->drupalProjects = new DrupalProjects($update_fetcher);
  }

  /**
   * Test getting project last stable release.
   *
   * @covers ::getCoreLastStableVersion
   */
  public function testGetProjectLastStableRelease() {
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
  public function testGetCoreLastMinorVersion() {
    $actual = $this->drupalProjects->getCoreLastMinorVersion();
    // The last minor version is always with patch level 0.
    $this->assertSame('8.8.0', $actual['version']);
  }

}
