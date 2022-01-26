<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Parser;

use Drupal\druki_author\Data\AuthorList;
use Drupal\druki_author\Data\AuthorsFile;
use Drupal\druki_author\Parser\AuthorsFileParser;
use Drupal\Tests\druki_content\Traits\SourceContentProviderTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for author file parser.
 *
 * @coversDefaultClass \Drupal\druki_author\Parser\AuthorsFileParser
 */
final class AuthorsFilerParserTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * Tests that parser works as expected.
   */
  public function testParser(): void {
    $directory = $this->setupFakeSourceDir();
    $authors_file = new AuthorsFile($directory->url() . '/authors/authors.json');
    $parser = new AuthorsFileParser();
    $result = $parser->parse($authors_file);
    $this->assertInstanceOf(AuthorList::class, $result);
    $this->assertEquals(2, $result->getIterator()->count());
  }

}
