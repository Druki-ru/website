<?php

namespace Drupal\Tests\druki_content\ExistingSite\Plugin\Block;

use Drupal\Tests\druki_content\Trait\DrukiContentCreationTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for category navigations.
 *
 * @covers \Drupal\druki_content\Plugin\Block\CategoryNavigationBlock
 */
final class CategoryNavigationBlockTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Tests that nothing is shown on page if no category set.
   */
  public function testNoCategorySet(): void {
    $content = $this->createDrukiContent(['type' => 'documentation']);
    $this->drupalGet($content->toUrl());
    $this->assertSession()->elementNotExists('css', '.block--category-navigation');
  }

  /**
   * Tests when category is set.
   */
  public function testCategorySet(): void {
    $category_name = 'flugegeheimen';
    $content_1 = $this->createDrukiContent([
      'type' => 'documentation',
      'category' => [
        'area' => $category_name,
        'order' => 1,
      ],
    ]);
    $content_2 = $this->createDrukiContent([
      'type' => 'documentation',
      'category' => [
        'area' => $category_name,
        'order' => 2,
      ],
    ]);
    $content_3 = $this->createDrukiContent([
      'type' => 'documentation',
      'category' => [
        'area' => $category_name,
        'order' => 3,
        'title' => 'foo-bar',
      ],
    ]);

    $this->drupalGet($content_1->toUrl());

    $assert_session = $this->assertSession();
    $assert_session->elementExists('css', '.block--category-navigation');
    $assert_session->elementTextContains('css', '.block--category-navigation .block__title', $category_name);
    $assert_session->elementsCount('css', '.block--category-navigation .category-navigation__menu-item-link', 3);
    $assert_session->linkExists($content_1->label());
    $assert_session->linkExists($content_2->label());
    $assert_session->linkExists('foo-bar');
    $assert_session->linkByHrefExists($content_1->toUrl()->toString());
    $assert_session->linkByHrefExists($content_2->toUrl()->toString());
    $assert_session->linkByHrefExists($content_3->toUrl()->toString());
    // Check that current page link marked as active.
    $active_link = $assert_session->elementExists('css', '.category-navigation__menu-item-link--active');
    $this->assertEquals($content_1->toUrl()->toString(), $active_link->getAttribute('href'));
  }

}
