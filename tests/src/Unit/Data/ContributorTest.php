<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Data;

use Drupal\druki\Data\Contributor;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for contributor value object.
 *
 * @coversDefaultClass \Drupal\druki\Data\Contributor
 */
final class ContributorTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $contributor = new Contributor('username', 'john.doe@example.com');
    $this->assertEquals('username', $contributor->getUsername());
    $this->assertEquals('john.doe@example.com', $contributor->getEmail());
  }

}
