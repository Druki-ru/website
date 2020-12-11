<?php

namespace Druki\Tests\Unit\Drupal;

use Drupal\druki\Drupal\DrupalProjects;
use Drupal\Tests\UnitTestCase;
use Drupal\update\UpdateFetcherInterface;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Test druki.drupal_projects service.
 *
 * @coversDefaultClass \Drupal\druki\Drupal\DrupalProjects
 */
class DrupalProjectsTest extends UnitTestCase {

  /**
   * The drupal projects service.
   *
   * @var \Drupal\druki\Drupal\DrupalProjects
   */
  protected $drupalProjects;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->drupalProjects = new DrupalProjects($this->buildUpdateFetcher());
  }

  /**
   * Builds mock for UpdateFetcherInterface.
   *
   * @return \Drupal\update\UpdateFetcherInterface
   *   The update fetcher.
   */
  protected function buildUpdateFetcher(): UpdateFetcherInterface {
    $prophecy = $this->prophesize(UpdateFetcherInterface::class);

    $fetch_project_data = new MethodProphecy($prophecy, 'fetchProjectData', [['name' => 'drupal']]);
    $fetch_project_data->willReturn(file_get_contents(__DIR__ . '/../../../fixtures/drupal-release-history.xml'));
    $prophecy->addMethodProphecy($fetch_project_data);

    return $prophecy->reveal();
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
