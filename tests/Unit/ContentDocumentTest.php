<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\druki_content\Data\ContentMetadata;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content document value object.
 *
 * @covers \Drupal\druki_content\Data\ContentDocument
 */
final class ContentDocumentTest extends UnitTestCase {

  /**
   * Tests value object behavior.
   *
   * @return void
   */
  public function testObject(): void {
    $language = 'ru';
    $metadata_data = [
      'title' => 'This is title',
      'slug' => 'foo/bar',
    ];
    $metadata = ContentMetadata::createFromArray($metadata_data);
    // @todo Add some sample data when object will be completed.
    $content = new Content();
    $document = new ContentDocument($language, $metadata, $content);
    $this->assertEquals($language, $document->getLanguage());
    $this->assertEquals($metadata_data['title'], $document->getMetadata()->getTitle());
    $this->assertEquals($metadata_data['slug'], $document->getMetadata()->getSlug());
    $this->assertSame($content, $document->getContent());
  }

}
