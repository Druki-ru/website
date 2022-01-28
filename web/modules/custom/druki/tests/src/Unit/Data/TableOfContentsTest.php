<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Unit\Data;

use Drupal\druki\Data\TableOfContents;
use Drupal\druki\Data\TableOfContentsHeading;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for Table of Contents.
 *
 * @coversDefaultClass \Drupal\druki\Data\TableOfContents
 */
final class TableOfContentsTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $toc = new TableOfContents();
    $this->assertEquals(0, $toc->getIterator()->count());

    $heading_1 = new TableOfContentsHeading('Link 1', 2);
    $toc->addHeading($heading_1);
    $heading_2 = new TableOfContentsHeading('Link 2', 3);
    $toc->addHeading($heading_2);
    $heading_3 = new TableOfContentsHeading('Link 3', 2);
    $toc->addHeading($heading_3);
    $heading_4 = new TableOfContentsHeading('Link 4', 3);
    $toc->addHeading($heading_4);
    $heading_5 = new TableOfContentsHeading('Link 5', 4);
    $toc->addHeading($heading_5);
    $heading_6 = new TableOfContentsHeading('Link 6', 2);
    $toc->addHeading($heading_6);

    $this->assertEquals(6, $toc->getIterator()->count());
    $this->assertEquals($heading_1, $toc->getIterator()->current());
    $expected_tree = [
      [
        'heading' => $heading_1,
        'children' => [
          ['heading' => $heading_2, 'children' => []],
        ],
      ],
      [
        'heading' => $heading_3,
        'children' => [
          [
            'heading' => $heading_4,
            'children' => [
              [
                'heading' => $heading_5,
                'children' => [],
              ],
            ],
          ],
        ],
      ],
      [
        'heading' => $heading_6,
        'children' => [],
      ],
    ];
    $this->assertEquals($expected_tree, $toc->toTreeArray());
  }

}
