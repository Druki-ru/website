<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\DataType;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\druki_content\Data\ContentMetadata;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content document data type plugin.
 *
 * @coversDefaultClass \Drupal\druki_content\Plugin\DataType\ContentDocument
 */
final class ContentDocumentTest extends ExistingSiteBase {

  /**
   * Tests that plugin works as expected.
   */
  public function testPlugin(): void {
    /** @var \Drupal\Core\TypedData\TypedDataManagerInterface $typed_data_manager */
    $typed_data_manager = $this->container->get('typed_data_manager');
    $druki_content_document_definition = $typed_data_manager->createDataDefinition('druki_content_document');
    $druki_content_document = $typed_data_manager->create($druki_content_document_definition);
    $content_document = new ContentDocument('ru', new ContentMetadata(), new Content());

    $this->assertEmpty($druki_content_document->getValue());
    $this->assertNull($druki_content_document->getContentDocument());
    $druki_content_document->setContentDocument($content_document);
    $this->assertEquals(\serialize($content_document), $druki_content_document->getValue());
    $this->assertEquals($content_document, $druki_content_document->getContentDocument());
    $druki_content_document->setValue(NULL);
    $this->assertNull($druki_content_document->getContentDocument());
    $druki_content_document->setValue($content_document);
    $this->assertEquals($content_document, $druki_content_document->getContentDocument());
  }

}
