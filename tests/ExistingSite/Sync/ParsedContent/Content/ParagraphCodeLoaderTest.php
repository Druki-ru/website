<?php

namespace Druki\Tests\ExistingSite\Sync\ParsedContent\Content;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphCode;
use Drupal\paragraphs\ParagraphInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for code loader into paragraph.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphCodeLoader
 */
final class ParagraphCodeLoaderTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Testing saving content into paragraph.
   */
  public function testProcess(): void {
    $paragraph_code = new ParagraphCode("echo 'Hello World!';");
    $druki_content = $this->createDrukiContent();
    $paragraph_code_loader = $this->container->get('druki_content.parsed_content_loader.paragraph_code');
    $paragraph_code_loader->process($paragraph_code, $druki_content);

    /** @var \Drupal\entity_reference_revisions\Plugin\Field\FieldType\EntityReferenceRevisionsItem $first_paragraph */
    $first_paragraph_item = $druki_content->get('content')->first();
    $paragraph = $first_paragraph_item->entity;
    $this->assertTrue($paragraph instanceof ParagraphInterface);
    $this->assertSame($paragraph_code->getParagraphType(), $paragraph->bundle());
    $this->assertSame($paragraph_code->getContent(), $paragraph->get('druki_textarea_formatted')->value);
  }

}
