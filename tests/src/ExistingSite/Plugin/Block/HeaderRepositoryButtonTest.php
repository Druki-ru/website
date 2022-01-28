<?php

namespace Druki\Tests\ExistingSite\Plugin\Block;

use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for header repository button.
 *
 * @coversDefaultClass \Drupal\druki\Plugin\Block\HeaderRepositoryButton
 */
final class HeaderRepositoryButtonTest extends ExistingSiteBase {

  /**
   * Tests that button is rendered on page.
   */
  public function testButtonIsOnPage(): void {
    $this->drupalGet(Url::fromRoute('<front>'));
    $this->assertSession()->elementExists('css', '.header-repository-button');
  }

}
