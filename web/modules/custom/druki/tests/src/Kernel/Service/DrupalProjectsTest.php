<?php

namespace Drupal\Tests\druki\Kernel\Service;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * Test druki.drupal_projects service.
 *
 * @coversDefaultClass \Drupal\druki\Service\DrupalProjects
 */
class DrupalProjectsTest extends KernelTestBase implements ServiceModifierInterface {

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
    $this->assertRegExp('/[0-9]+.[0-9]+.[0-9]+/', $actual['version']);
    // The stable release is always published.
    $this->assertEqual($actual['status'], 'published');
  }

  /**
   * Test getting project last minor version.
   *
   * @covers ::getCoreLastMinorVersion
   */
  public function testGetCoreLastMinorVersion() {
    $actual = $this->drupalProjects->getCoreLastMinorVersion();
    // The last minor version is always with patch level 0.
    $this->assertRegExp('/[0-9]+.[0-9]+.0/', $actual['version']);
  }

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // @see https://www.drupal.org/project/drupal/issues/2571475
    $container->removeDefinition('test.http_client.middleware');
  }

}
