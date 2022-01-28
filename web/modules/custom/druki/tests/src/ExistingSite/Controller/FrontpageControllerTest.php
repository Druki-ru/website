<?php

namespace Drupal\Tests\druki\ExistingSite\Controller;

use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides frontpage controller.
 *
 * @coversDefaultClass \Drupal\druki\Controller\FrontpageController
 */
final class FrontpageControllerTest extends ExistingSiteBase {

  /**
   * Tests frontpage.
   *
   * @covers ::build
   */
  public function testFrontpage(): void {
    $this->drupalGet(Url::fromRoute('<front>'));
    $this->assertSession()->elementExists('css', '.frontpage-hero');
  }

}
