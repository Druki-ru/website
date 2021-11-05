<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Generator;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\druki_content\Data\ContentMetadata;
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
    /** @var \Drupal\druki_content\Generator\ContentDocumentChecksumGenerator $generator */
    $generator = $this->container->get('druki_content.generator.content_document_checksum');
    $content_document_1 = new ContentDocument('ru', new ContentMetadata(), new Content());
    $content_document_2 = new ContentDocument('en', new ContentMetadata(), new Content());

    $checksum_1 = $generator->generate($content_document_1);
    $checksum_2 = $generator->generate($content_document_2);
    $this->assertNotEquals($checksum_1, $checksum_2);

    $checksum_3 = $generator->generate($content_document_1);
    $this->assertEquals($checksum_1, $checksum_3);
  }

}
