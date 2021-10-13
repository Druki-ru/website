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
    // Currently it causes GitHub Actions to fails for some reason. Since it
    // will queued for refactoring, there is no point trying to fix it. Plus it
    // is not that important.
    #$this->markTestSkipped('This test doesnt work in GitHub Actions. See https://github.com/Druki-ru/website/issues/64.');
    $this->drupalGet(Url::fromRoute('<front>'));
    $assert_session = $this->assertSession();
    $switcher = $assert_session->elementExists('css', '.region-mobile-toolbar .js-dark-mode-switcher');
    $switcher->click();
    $assert_session->elementAttributeContains('css', 'html', 'data-theme', 'dark');
  }

}
