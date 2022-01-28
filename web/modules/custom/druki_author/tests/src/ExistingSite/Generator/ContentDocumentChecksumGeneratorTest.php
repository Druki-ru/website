<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\ExistingSite\Generator;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentDocument;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content document checksum generator.
 *
 * @coversDefaultClass \Drupal\druki_content\Generator\ContentDocumentChecksumGenerator
 */
final class ContentDocumentChecksumGeneratorTest extends ExistingSiteBase {

  /**
   * Tests that generator works as expected.
   */
  public function testGenerator(): void {
    $typed_data_manager = $this->container->get('typed_data_manager');
    $content_metadata_definition = $typed_data_manager->createDataDefinition('druki_content_documentation_metadata');
    $content_metadata = $typed_data_manager->create($content_metadata_definition, ['foo' => 'bar']);

    /** @var \Drupal\druki_content\Generator\ContentDocumentChecksumGenerator $generator */
    $generator = $this->container->get('druki_content.generator.content_document_checksum');
    $content_document_1 = new ContentDocument('ru', $content_metadata, new Content());
    $content_document_2 = new ContentDocument('en', $content_metadata, new Content());

    $checksum_1 = $generator->generate($content_document_1);
    $checksum_2 = $generator->generate($content_document_2);
    $this->assertNotEquals($checksum_1, $checksum_2);

    $checksum_3 = $generator->generate($content_document_1);
    $this->assertEquals($checksum_1, $checksum_3);
  }

}
