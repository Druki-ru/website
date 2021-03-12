<?php

namespace Druki\Tests\ExistingSite\Sync\SourceContent;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\Component\Utility\Crypt;
use Drupal\druki_content\Sync\ParsedContent\ParsedContent;
use Drupal\druki_content\Sync\SourceContent\ParsedSourceContent;
use Drupal\druki_content\Sync\SourceContent\SourceContent;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for source content parser.
 */
final class SourceContentParserTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * The source content parser.
   *
   * @var \Drupal\druki_content\Sync\SourceContent\SourceContentParser
   */
  protected $parser;

  /**
   * The source directory.
   *
   * @var \org\bovigo\vfs\vfsStreamDirectory
   */
  protected $sourceDirectory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->sourceDirectory = $this->setupFakeSourceDir();
    $this->parser = $this->container->get('druki_content.source_content_parser');
  }

  /**
   * Tests parsers.
   */
  public function testParser(): void {
    $realpath = $this->sourceDirectory->url() . '/docs/ru/drupal/index.md';
    $relative_pathname = 'docs/ru/drupal/index.md';
    $language = 'ru';
    $source_content = new SourceContent($realpath, $relative_pathname, $language);
    $parsed_source = $this->parser->parse($source_content);
    $this->assertTrue($parsed_source instanceof ParsedSourceContent);

    $source = $parsed_source->getSource();
    $this->assertTrue($source instanceof SourceContent);
    $this->assertEquals('Drupal description.', $source->getContent());

    $parsed_content = $parsed_source->getParsedSource();
    $this->assertTrue($parsed_content instanceof ParsedContent);
    $this->assertEquals('<p>Drupal description.</p>', $parsed_content->getContent()->pop()->getContent());

    $expected_hash = Crypt::hashBase64(\serialize($source) . \serialize($parsed_content));
    $this->assertEquals($expected_hash, $parsed_source->getSourceHash());
  }

  /**
   * Tests when file is not readable for some reason.
   */
  public function testBrokenFile(): void {
    $source_content = new SourceContent('foo.md', 'foo.md', 'ru');
    $result = $this->parser->parse($source_content);
    $this->assertNull($result);
  }

}
