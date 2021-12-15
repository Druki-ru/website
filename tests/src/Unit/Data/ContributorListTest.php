<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Data;

use Drupal\druki\Data\Contributor;
use Drupal\druki\Data\ContributorList;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for contributors list.
 *
 * @coversDefaultClass \Drupal\druki\Data\ContributorList
 */
final class ContributorListTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $contributor_1 = new Contributor('John Doe', 'john.doe@example.com');
    $contributor_2 = new Contributor('Jane Doe', 'jane.doe@example.com');
    $list = new ContributorList();
    $list->addContributor($contributor_1);
    $list->addContributor($contributor_2);

    $this->assertSame($contributor_1, $list->getIterator()->offsetGet(0));
    $this->assertSame($contributor_2, $list->getIterator()->offsetGet(1));
  }

}
