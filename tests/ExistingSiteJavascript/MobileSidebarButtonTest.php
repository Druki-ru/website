<?php

namespace Druki\Tests\ExistingSiteJavascript;

use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteSelenium2DriverTestBase;

/**
 * Provides test for mobile sidebar button.
 *
 * @coversDefaultClass \Drupal\druki\Plugin\Block\MobileSidebarButton
 */
final class MobileSidebarButtonTest extends ExistingSiteSelenium2DriverTestBase {

  /**
   * Tests that button is exists and works.
   */
  public function testButton(): void {
    $this->markTestSkipped('This test must be improved in https://github.com/Druki-ru/website/issues/47');

    // If we install website from configuration, there will be 0 menu items.
    // We need to create them. But this is not such an important test, so it
    // skipped to push PHPUnit into GitHub Actions.
    $this->drupalGet(Url::fromRoute('<front>'));
    // Chrome 'Moto G4' size.
    $this->getDriverInstance()->resizeWindow(360, 640);

    $assert_session = $this->assertSession();
    $sidebar_button = $assert_session->elementExists('css', '.js-mobile-sidebar-button');
    $sidebar = $assert_session->elementExists('css', '.js-mobile-sidebar');

    // Check that button toggle visibility of the sidebar.
    $this->assertEquals(FALSE, $sidebar->isVisible());
    $sidebar_button->click();
    $this->assertEquals(TRUE, $sidebar->isVisible());

    // Click on overlay must close sidebar.
    $overlay = $assert_session->elementExists('css', '.js-mobile-sidebar-overlay');
    $overlay->click();
    $this->assertEquals(FALSE, $sidebar->isVisible());

    // Test that 'Close' button also works.
    $sidebar_button->click();
    $this->assertEquals(TRUE, $sidebar->isVisible());
    $close_button = $assert_session->elementExists('css', '.js-mobile-sidebar-close');
    $close_button->click();
    $this->assertEquals(FALSE, $sidebar->isVisible());
  }

}
