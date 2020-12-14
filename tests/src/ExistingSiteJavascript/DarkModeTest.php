<?php

namespace Druki\Tests\ExistingSiteJavascript;

use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteSelenium2DriverTestBase;

/**
 * Provides test for dark mode.
 */
final class DarkModeTest extends ExistingSiteSelenium2DriverTestBase {

  /**
   * Test that dark mode switch works as expected.
   */
  public function testDarkModeSwitch(): void {
    $this->drupalGet(Url::fromRoute('<front>'));
    $assert_session = $this->assertSession();
    $switcher = $assert_session->elementExists('css', '.region-mobile-toolbar .js-dark-mode-switcher');
    $switcher->click();
    $assert_session->elementAttributeContains('css', 'html', 'data-theme', 'dark');
  }

}
