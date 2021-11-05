<?php

namespace Druki\Tests\ExistingSite\Controller;

use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides pages controller controller.
 *
 * @coversDefaultClass \Drupal\druki\Controller\PagesController
 */
final class PagesControllerTest extends ExistingSiteBase {

  /**
   * Tests download page.
   *
   * @covers ::downloadPage
   */
  public function testDownloadPage(): void {
    $this->drupalGet(Url::fromRoute('druki.download'));
    $this->assertSession()->elementExists('css', '.druki-page-download');
  }

  /**
   * Tests wiki page.
   *
   * @covers ::wikiPage
   */
  public function testWikiPage(): void {
    $this->drupalGet(Url::fromRoute('druki.wiki'));
    $this->assertSession()->elementExists('css', '.druki-wiki-page');
  }

}
