<?php

namespace Druki\Tests\ExistingSite\Sync\ParsedContent\Content;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphNote;
use Drupal\paragraphs\ParagraphInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for note loader into paragraph.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphNoteLoader
 */
final class ParagraphNoteLoaderTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Testing saving content into paragraph.
   */
  public function testProcess(): void {
    $paragraph_note = new ParagraphNote('warning', 'This is the warning!');
    $druki_content = $this->createDrukiContent();
    $paragraph_note_loader = $this->container->get('druki_content.parsed_content_loader.paragraph_note');
    $paragraph_note_loader->process($paragraph_note, $druki_content);

    /** @var \Drupal\entity_reference_revisions\Plugin\Field\FieldType\EntityReferenceRevisionsItem $first_paragraph */
    $first_paragraph_item = $druki_content->get('content')->first();
    $paragraph = $first_paragraph_item->entity;
    $this->assertTrue($paragraph instanceof ParagraphInterface);
    $this->assertSame($paragraph_note->getType(), $paragraph->get('druki_note_type')->value);
    $this->assertSame($paragraph_note->getContent(), $paragraph->get('druki_textarea_formatted')->value);
  }

}
