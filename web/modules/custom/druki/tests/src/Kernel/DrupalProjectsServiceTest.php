<?php

namespace Drupal\Tests\druki_markdown\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;

/**
 * Test druki.drupal_projects service.
 */
class DrupalProjectsServiceTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['druki', 'system', 'update'];

  /**
   * The drupal projects service.
   *
   * @var \Drupal\druki\Service\DrupalProjects|object
   */
  protected $drupalProjects;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    define('DRUPAL_TEST_IN_CHILD_SITE', TRUE);
    $this->drupalProjects = $this->container->get('druki.drupal_projects');
  }

  /**
   * Test getting project last stable release.
   */
  public function testGetProjectLastStableRelease() {
    $actual = $this->drupalProjects->getProjectLastStableRelease('drupal');
    dump($actual);
  }

}
