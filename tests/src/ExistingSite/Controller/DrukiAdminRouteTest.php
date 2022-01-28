<?php

namespace Druki\Tests\ExistingSite\Controller;

use Druki\Tests\ExistingSite\DrukiExistingSiteBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides test for test for 'druki.admin' route.
 */
final class DrukiAdminRouteTest extends DrukiExistingSiteBase {

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
    $this->assertSession()
      ->elementTextContains('css', '.page-title', new TranslatableMarkup('Druki Settings'));
  }

}
