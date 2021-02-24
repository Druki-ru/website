<?php

namespace Druki\Tests\ExistingSite\Sync\ParsedContent\Content;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphHeading;
use Drupal\paragraphs\ParagraphInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for heading loader into paragraph.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphHeadingLoader
 */
final class ParagraphHeadingLoaderTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Testing saving content into paragraph.
   */
  public function testProcess(): void {
    $paragraph_heading = new ParagraphHeading('h2', 'Foo bar');
    $druki_content = $this->createDrukiContent();
    $paragraph_heading_loader = $this->container->get('druki_content.parsed_content_loader.paragraph_heading');
    $paragraph_heading_loader->process($paragraph_heading, $druki_content);

    /** @var \Drupal\entity_reference_revisions\Plugin\Field\FieldType\EntityReferenceRevisionsItem $first_paragraph */
    $first_paragraph_item = $druki_content->get('content')->first();
    $paragraph = $first_paragraph_item->entity;
    $this->assertTrue($paragraph instanceof ParagraphInterface);
    $this->assertSame($paragraph_heading->getParagraphType(), $paragraph->bundle());
    $this->assertSame($paragraph_heading->getLevel(), $paragraph->get('druki_heading_level')->value);
    $this->assertSame($paragraph_heading->getContent(), $paragraph->get('druki_textfield_formatted')->value);
  }

}
