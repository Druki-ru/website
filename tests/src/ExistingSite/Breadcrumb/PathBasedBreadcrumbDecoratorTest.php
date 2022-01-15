<?php

namespace Druki\Tests\ExistingSite\Breadcrumb;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for path based breadcrumb decorator.
 *
 * @coversDefaultClass \Drupal\druki\Breadcrumb\PathBasedBreadcrumbDecorator
 */
final class PathBasedBreadcrumbDecoratorTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Test decorator title for link change.
   *
   * @covers ::build
   */
  public function testDecorator(): void {
    $content = $this->createDrukiContent([
      'type' => 'documentation',
      'slug' => 'wiki/test/abcd',
    ]);
    $this->drupalGet($content->toUrl());
    $this->assertSession()->elementExists('css', '.breadcrumb__item-link--current');
    $this->assertSession()->elementTextContains('css', '.breadcrumb__item-link--current', new TranslatableMarkup('Wiki'));
  }

}
