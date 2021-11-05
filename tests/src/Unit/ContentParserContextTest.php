<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentParserContext;
use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\Tests\UnitTestCase;

/**
 * Provides tests for content parser context object.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentParserContext
 */
final class ContentParserContextTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $context = new ContentParserContext();

    $this->assertNull($context->getContentSourceFile());
    $content_source_file = new ContentSourceFile('fake://drupal.md', 'drupal.md', 'ru');
    $context->setContentSourceFile($content_source_file);
    $this->assertSame($content_source_file, $context->getContentSourceFile());

    $this->assertNull($context->getContent());
    $content = new Content();
    $context->setContent($content);
    $this->assertSame($content, $context->getContent());
  }

}
