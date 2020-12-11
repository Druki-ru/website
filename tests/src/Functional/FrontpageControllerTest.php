<?php

namespace Drupal\Tests\druki\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Provides frontpage controller.
 *
 * @coversDefaultClass \Drupal\druki\Controller\FrontpageController
 */
final class FrontpageControllerTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable9';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['druki', 'system'];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    user_role_grant_permissions('anonymous', ['access content']);
  }

  /**
   * Test page response.
   *
   * @covers ::build
   */
  public function testPage(): void {
    $this->drupalGet('/frontpage');
    // Since this page just for URI and actual markup added via theme we check
    // it exists and response as expected, that's it.
    $this->assertSession()->statusCodeEquals(200);
  }

}
