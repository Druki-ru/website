<?php

namespace Druki\Tests\ExistingSite\Sync\ParsedContent\Content;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphText;
use Drupal\paragraphs\ParagraphInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for text loader into paragraph.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphTextLoader
 */
final class ParagraphTextLoaderTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Testing saving content into paragraph.
   */
  public function testProcess(): void {
    $paragraph_text = new ParagraphText('This is the content.');
    $druki_content = $this->createDrukiContent();
    $paragraph_text_loader = $this->container->get('druki_content.parsed_content_loader.paragraph_text');
    $paragraph_text_loader->process($paragraph_text, $druki_content);

    /** @var \Drupal\entity_reference_revisions\Plugin\Field\FieldType\EntityReferenceRevisionsItem $first_paragraph */
    $first_paragraph_item = $druki_content->get('content')->first();
    $paragraph = $first_paragraph_item->entity;
    $this->assertTrue($paragraph instanceof ParagraphInterface);
    $this->assertSame($paragraph_text->getParagraphType(), $paragraph->bundle());
    $this->assertSame($paragraph_text->getContent(), $paragraph->get('druki_textarea_formatted')->value);
    $this->assertSame($paragraph_text_loader->getDefaultTextFilter(), $paragraph->get('druki_textarea_formatted')->format);
  }

}
