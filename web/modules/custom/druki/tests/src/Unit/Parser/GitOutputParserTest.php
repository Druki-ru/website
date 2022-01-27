<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Unit\Parser;

use Drupal\druki\Parser\GitOutputParser;
use Drupal\Tests\UnitTestCase;

/**
 * Provides git output parser.
 *
 * @coversDefaultClass \Drupal\druki\Parser\GitOutputParser
 */
final class GitOutputParserTest extends UnitTestCase {

  /**
   * Tests that parsing contributors log is working as expected.
   *
   * @covers ::parseContributorsLog
   */
  public function testParseContributorsLog(): void {
    $log = <<<'TEXT'
      20 John Wick <john.wick@example.com>
      2 John Doe <john.doe@example.com>
      1 Jane Doe <jane.doe@example.com>
    TEXT;

    $result = GitOutputParser::parseContributorsLog($log);
    $this->assertEquals('John Wick', $result->getIterator()->offsetGet(0)->getUsername());
    $this->assertEquals('john.wick@example.com', $result->getIterator()->offsetGet(0)->getEmail());
    $this->assertEquals('John Doe', $result->getIterator()->offsetGet(1)->getUsername());
    $this->assertEquals('john.doe@example.com', $result->getIterator()->offsetGet(1)->getEmail());
    $this->assertEquals('Jane Doe', $result->getIterator()->offsetGet(2)->getUsername());
    $this->assertEquals('jane.doe@example.com', $result->getIterator()->offsetGet(2)->getEmail());
  }

}
