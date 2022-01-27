<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_redirect\Unit\Data;

use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides test for redirect file list object.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\RedirectFileList
 */
final class RedirectFileListTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    vfsStream::setup();

    vfsStream::create([
      'redirects.csv' => '',
    ]);

    $list = new RedirectFileList();
    $this->assertEmpty($list->getIterator()->getArrayCopy());

    $redirect_file = new RedirectFile(vfsStream::url('root/redirects.csv'), 'ru');
    $list->addFile($redirect_file);
    $this->assertSame($redirect_file, $list->getIterator()->current());
    $this->assertCount(1, $list->getIterator());
  }

}
