<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Data;

use Drupal\druki_redirect\Data\RedirectUrl;
use Drupal\Tests\UnitTestCase;

/**
 * Provides redirect value object test.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\RedirectUrl
 */
final class RedirectUrlTest extends UnitTestCase {

  /**
   * @return void
   */
  public function testObject(): void {
    $url = RedirectUrl::createFromUserInput('/foo-bar');
    $this->assertEquals('/foo-bar', $url->getPath());
    $this->assertEquals([], $url->getQuery());
    $this->assertEquals('', $url->getFragment());

    $url = RedirectUrl::createFromUserInput('/foo-bar?with=query');
    $this->assertEquals('/foo-bar', $url->getPath());
    $this->assertEquals(['with' => 'query'], $url->getQuery());
    $this->assertEquals('', $url->getFragment());

    $url = RedirectUrl::createFromUserInput('/foo-bar#foo-bar');
    $this->assertEquals('/foo-bar', $url->getPath());
    $this->assertEquals([], $url->getQuery());
    $this->assertEquals('foo-bar', $url->getFragment());
  }

}
