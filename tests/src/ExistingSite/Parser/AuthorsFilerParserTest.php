<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Parser;

use Drupal\druki_author\Data\AuthorList;
use Drupal\druki_author\Data\AuthorsFile;
use Drupal\druki_author\Parser\AuthorsFileParser;
use org\bovigo\vfs\vfsStream;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for author file parser.
 *
 * @coversDefaultClass \Drupal\druki_author\Parser\AuthorsFileParser
 */
final class AuthorsFilerParserTest extends ExistingSiteBase {

  /**
   * Tests that parser works as expected.
   */
  public function testParser(): void {
    $authors_contents = <<<'JSON'
    {
      "$schema": "https://druki.ru/modules/custom/druki_author/authors.schema.json",
      "dries": {
        "name": {
          "given": "Dries",
          "family": "Buytaert"
        },
        "org": {
          "name": "Acquia",
          "unit": "CTO"
        },
        "country": "GB",
        "homepage": "https://dri.es/",
        "description": {
          "en": "I'm the founder of Drupal and Acquia. I've been working on Open Source and an Open Web for 20+ years. I'm also a blogger, photographer, traveler, investor, husband, and father of two wonderful kids."
        },
        "image": "image/dries.jpg",
        "identification": {
          "email": ["dries@buytaert.net", "dries@acquia.com"]
        }
      },
      "zuck": {
        "name": {
          "family": "Mark",
          "given": "Zuckerberg"
        },
        "org": {
          "name": "Meta",
          "unit": "CEO"
        },
        "country": "US",
        "homepage": "https://meta.com"
      }
    }
    JSON;

    vfsStream::setup();
    vfsStream::create([
      'authors.json' => $authors_contents,
      'image' => [
        'dries.jpg' => '',
      ],
    ]);

    $authors_file = new AuthorsFile(vfsStream::url('root/authors.json'));
    $parser = new AuthorsFileParser();
    $result = $parser->parse($authors_file);
    $this->assertInstanceOf(AuthorList::class, $result);
    $this->assertEquals(2, $result->getIterator()->count());
  }

}
