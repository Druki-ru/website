<?php

namespace Drupal\Tests\druki\Kernel\Service;

use Drupal\KernelTests\KernelTestBase;

/**
 * Test druki.drupal_projects service.
 *
 * @coversDefaultClass \Drupal\druki\Service\DrupalProjects
 */
class DrupalProjectsTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['druki', 'system', 'update'];

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
    $this->drupalProjects = $this->container->get('druki.drupal_projects');
  }

  /**
   * Test getting project last stable release.
   *
   * @covers ::getCoreLastStableVersion
   */
  public function testGetProjectLastStableRelease() {
    $actual = $this->drupalProjects->getCoreLastStableVersion();
  }

}
