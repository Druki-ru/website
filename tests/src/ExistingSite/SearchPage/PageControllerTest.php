<?php

namespace Druki\Tests\ExistingSite\SearchPage;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\search_api\Entity\Index;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test search page.
 *
 * @covers \Drupal\druki_search\SearchPage\PageController
 *
 * @todo Improve test with indexing.
 */
final class PageControllerTest extends ExistingSiteBase {

  /**
   * Tests empty request.
   */
  public function testEmpty(): void {
    $this->drupalGet('/search');
    $this->assertSession()->statusCodeEquals(200);
    $content = new TranslatableMarkup('Please enter some keywords.');
    $this->assertSession()->pageTextContainsOnce($content);
  }

}
