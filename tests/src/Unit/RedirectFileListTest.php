<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\Tests\druki_content\Traits\SourceContentProviderTrait;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for redirect file list object.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\RedirectFileList
 */
final class RedirectFileListTest extends UnitTestCase {

  use SourceContentProviderTrait;

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $root = $this->setupFakeSourceDir();
    $file_pathname = $root->url() . '/docs/ru/redirects.csv';

    $list = new RedirectFileList();
    $this->assertEmpty($list->getIterator()->getArrayCopy());

    $redirect_file = new RedirectFile($file_pathname, 'ru');
    $list->addFile($redirect_file);
    $this->assertEquals($redirect_file, $list->getIterator()->current());
    $this->assertCount(1, $list->getIterator());
  }

}
