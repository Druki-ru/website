<?php

namespace Drupal\Tests\druki_redirect\Unit\Data;

use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Tests redirect file value object.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\RedirectFile
 */
final class RedirectFileTest extends UnitTestCase {

  /**
   * Tests behavior with file that exists.
   */
  public function testExistedFile(): void {
    vfsStream::setup();

    $redirects_content = <<<'EOF'
    /foo-bar,/
    /foo-bar?with=query,/
    /foo-baz,/#fragment
    EOF;

    vfsStream::create([
      'docs' => [
        'ru' => [
          'redirects.csv' => $redirects_content,
        ],
      ],
    ]);

    $redirect_file = new RedirectFile(vfsStream::url('root/docs/ru/redirects.csv'), 'ru');
    $this->assertEquals($redirects_content, \file_get_contents($redirect_file->getPathname()));
    $this->assertEquals('ru', $redirect_file->getLanguage());
    $expected_hash = \hash('sha256', $redirects_content);
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
