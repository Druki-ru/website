<?php

namespace Druki\Tests\ExistingSite\Breadcrumb;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for path based breadcrumb decorator.
 *
 * @coversDefaultClass \Drupal\druki\Breadcrumb\PathBasedBreadcrumbDecorator
 */
final class PathBasedBreadcrumbDecorator extends ExistingSiteBase {

  /**
   * Test decorator title for link change.
   *
   * @covers ::build
   */
  public function testDecorator(): void {
    $this->drupalGet('/wiki/drupal');
    $this->assertSession()->elementExists('css', '.breadcrumb__item-link--current');
    $this->assertSession()->elementTextContains('css', '.breadcrumb__item-link--current', new TranslatableMarkup('Wiki'));
  }

}
