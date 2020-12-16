<?php

namespace Druki\Tests\ExistingSite\SearchPage;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test search page.
 *
 * @coversDefaultClass \Drupal\druki_search\SearchPage\PageController
 *
 * @todo Use fake entities when trait will be created.
 */
final class PageControllerTest extends ExistingSiteBase {

  /**
   * Tests empty request.
   */
  public function testEmpty(): void {
    $url = Url::fromRoute('druki_search.page');
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $content = new TranslatableMarkup("You didn't enter a search query.");
    $this->assertSession()->pageTextContainsOnce($content);
  }

  /**
   * Tests request with no results found.
   */
  public function testNoResults(): void {
    $keys = 'Michael Scott!';
    $url = Url::fromRoute('druki_search.page', [], [
      'query' => [
        'text' => $keys,
      ],
    ]);
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $page_title = new TranslatableMarkup('No results found for "%keys"', ['%keys' => $keys]);
    // Remove <em> wrapper for placeholder, this is markup, not text.
    $page_title = \strip_tags($page_title);
    $this->assertSession()->pageTextContainsOnce($page_title);
    $content = new TranslatableMarkup('No results found.');
    $this->assertSession()->pageTextContains($content);
  }

  /**
   * Tests request with results found.
   */
  public function testResults(): void {
    $keys = 'Drupal';
    $url = Url::fromRoute('druki_search.page', [], [
      'query' => [
        'text' => $keys,
      ],
    ]);
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $page_title = new TranslatableMarkup('Search results for "%keys"', ['%keys' => $keys]);
    // Remove <em> wrapper for placeholder, this is markup, not text.
    $page_title = \strip_tags($page_title);
    $this->assertSession()->pageTextContainsOnce($page_title);
    $this->assertSession()->elementExists('css', '.search-results__item');
  }

}
