<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\Unit\Data;

use Drupal\druki_author\Data\Author;
use Drupal\druki_author\Data\AuthorList;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for author list.
 *
 * @coversDefaultClass \Drupal\druki_author\Data\AuthorList
 */
final class AuthorListTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $list = new AuthorList();
    $this->assertEquals(0, $list->getIterator()->count());

    $author = new Author();
    $list->addAuthor($author);
    $this->assertEquals(1, $list->getIterator()->count());
    $this->assertSame($author, $list->getIterator()->current());
  }

}
