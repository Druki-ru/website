<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Data;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\druki_content\Plugin\DataType\DocumentationMetadataInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests content document value object.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentDocument
 */
final class ContentDocumentTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests value object behavior.
   */
  public function testObject(): void {
    $language = 'ru';
    $metadata_data = [
      'title' => 'This is title',
      'slug' => 'foo/bar',
    ];

    $content_metadata = $this->prophesize(DocumentationMetadataInterface::class);
    $content_metadata->getTitle()->willReturn($metadata_data['title']);
    $content_metadata->getSlug()->willReturn($metadata_data['slug']);

    $content = new Content();
    $document = new ContentDocument($language, $content_metadata->reveal(), $content);
    $this->assertEquals($language, $document->getLanguage());
    $this->assertEquals($metadata_data['title'], $document->getMetadata()
      ->getTitle());
    $this->assertEquals($metadata_data['slug'], $document->getMetadata()
      ->getSlug());
    $this->assertSame($content, $document->getContent());
  }

}
