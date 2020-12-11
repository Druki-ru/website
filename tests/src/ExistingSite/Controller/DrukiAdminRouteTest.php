<?php

namespace Druki\Tests\ExistingSite\Controller;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for test for 'druki.admin' route.
 */
final class DrukiAdminRouteTest extends ExistingSiteBase {

  /**
   * Test page from anonymous.
   */
  public function testAnonymous(): void {
    $this->drupalGet(Url::fromRoute('druki.admin'));
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Test page under admin user.
   */
  public function testAdmin(): void {
    $admin = $this->createUser([], 'admin_test', TRUE);
    $this->drupalLogin($admin);
    $this->drupalGet(Url::fromRoute('druki.admin'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->elementTextContains('css', '.page-title', new TranslatableMarkup('Druki Settings'));
  }

}
