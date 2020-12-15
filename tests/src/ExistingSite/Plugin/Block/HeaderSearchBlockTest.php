<?php

namespace Druki\Tests\ExistingSite\Plugin\Block;

use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for header search block.
 *
 * @coversDefaultClass \Drupal\druki\Plugin\Block\HeaderSearchBlock
 */
final class HeaderSearchBlockTest extends ExistingSiteBase {

  /**
   * Tests that search is rendered on page.
   */
  public function testSearchIsOnPage(): void {
    $this->drupalGet(Url::fromRoute('<front>'));
    $this->assertSession()->elementExists('css', '.druki-header-search');
  }

}
