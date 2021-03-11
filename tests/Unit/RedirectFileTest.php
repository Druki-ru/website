<?php

namespace Druki\Tests\Unit;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_content\Sync\Redirect\RedirectFile;
use Drupal\Tests\UnitTestCase;

/**
 * Tests redirect file value object.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\Redirect\RedirectFile
 */
final class RedirectFileTest extends UnitTestCase {

  use SourceContentProviderTrait;

  /**
   * Tests behavior with file that exists.
   */
  public function testExistedFile(): void {
    $root = $this->setupFakeSourceDir();
    $file_pathname = $root->url() . '/docs/ru/redirects.csv';
    $redirect_file = new RedirectFile($file_pathname, 'ru');
    $expected_content = \file_get_contents($file_pathname);
    $this->assertEquals($expected_content, \file_get_contents($redirect_file->getPathname()));
    $this->assertEquals('ru', $redirect_file->getLanguage());
    $expected_hash = \hash('sha256', $expected_content);
    $this->assertEquals($expected_hash, $redirect_file->getHash());
  }

  /**
   * Tests that class throws exception.
   */
  public function testNotExistedFile(): void {
    $this->expectException(\InvalidArgumentException::class);
    new RedirectFile('foo-bar.csv', 'ru');
  }

}
