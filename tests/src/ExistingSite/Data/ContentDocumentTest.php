<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Data;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentDocument;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Tests content document value object.
 *
 * @coversDefaultClass  \Drupal\druki_content\Data\ContentDocument
 */
final class ContentDocumentTest extends ExistingSiteBase {

  /**
   * Tests value object behavior.
   */
  public function testObject(): void {
    $language = 'ru';
    $metadata_data = [
      'title' => 'This is title',
      'slug' => 'foo/bar',
    ];

    $typed_data_manager = $this->container->get('typed_data_manager');
    $content_metadata_definition = $typed_data_manager->createDataDefinition('druki_content_documentation_metadata');
    $content_metadata = $typed_data_manager->create($content_metadata_definition, $metadata_data);

    $content = new Content();
    $document = new ContentDocument($language, $content_metadata, $content);
    $this->assertEquals($language, $document->getLanguage());
    $this->assertEquals($metadata_data['title'], $document->getMetadata()->getTitle());
    $this->assertEquals($metadata_data['slug'], $document->getMetadata()->getSlug());
    $this->assertSame($content, $document->getContent());
  }

}
