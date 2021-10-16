<?php

namespace Druki\Tests\ExistingSite\Sync\SourceContent;

use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\druki_content\Sync\ParsedContent\Content\ContentList;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphText;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatter;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue;
use Drupal\druki_content\Sync\ParsedContent\ParsedContent;
use Drupal\druki_content\Sync\SourceContent\ParsedSourceContent;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for loading (create / update) content from source.
 */
final class ParsedSourceContentLoaderTest extends ExistingSiteBase {

  /**
   * Test the creating and updating entities.
   */
  public function testLoader(): void {
    $source_content = new ContentSourceFile('fake://drupal.md', 'drupal.md', 'ru');
    $front_matter = new FrontMatter();
    $front_matter->add(new FrontMatterValue('title', 'Drupal Test Loader'));
    $front_matter->add(new FrontMatterValue('slug', 'test-loader'));
    $front_matter->add(new FrontMatterValue('core', '10'));
    $content_list = new ContentList();
    $content_list->add(new ParagraphText('Hello World!'));
    $parsed_content = new ParsedContent($front_matter, $content_list);
    $parsed_source_content = new ParsedSourceContent($source_content, $parsed_content);

    /** @var \Drupal\druki_content\Sync\SourceContent\ParsedSourceContentLoader $source_content_loader */
    $source_content_loader = $this->container->get('druki_content.parsed_source_content_loader');
    $druki_content = $source_content_loader->process($parsed_source_content);
    $this->markEntityForCleanup($druki_content);
    $this->assertEquals('Drupal Test Loader', $druki_content->label());

    // Now provide a bit updated, but the same content.
    $source_content = new ContentSourceFile('fake://drupal.md', 'drupal.md', 'ru');
    $front_matter = new FrontMatter();
    $front_matter->add(new FrontMatterValue('title', 'Drupal Test Label changed'));
    $front_matter->add(new FrontMatterValue('slug', 'test-loader'));
    $front_matter->add(new FrontMatterValue('core', '10'));
    $content_list = new ContentList();
    $content_list->add(new ParagraphText('Hello World!'));
    $parsed_content = new ParsedContent($front_matter, $content_list);
    $parsed_source_content = new ParsedSourceContent($source_content, $parsed_content);
    /** @var \Drupal\druki_content\Sync\SourceContent\ParsedSourceContentLoader $source_content_loader */
    $source_content_loader = $this->container->get('druki_content.parsed_source_content_loader');
    $druki_content_2 = $source_content_loader->process($parsed_source_content);

    // We expect that it will found previously created content and update it.
    $this->assertEquals($druki_content->id(), $druki_content_2->id());
  }

}
