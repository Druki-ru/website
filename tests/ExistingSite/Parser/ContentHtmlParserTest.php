<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Parser;

use Drupal\druki_content\Data\Content;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for content HTML parser.
 *
 * @coversDefaultClass  \Drupal\druki_content\Parser\ContentHtmlParser
 */
final class ContentHtmlParserTest extends ExistingSiteBase {

  /**
   * Tests that parser working and returns specific type.
   *
   * @covers ::parse()
   */
  public function testParse(): void {
    $parser = $this->container->get('druki_content.parser.content_html_parser');
    $result = $parser->parse('<p>Hello World!</p>');
    $this->assertInstanceOf(Content::class, $result);
  }

}
