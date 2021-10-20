<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Render;

use Druki\Tests\Traits\SourceContentProviderTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Tests that rendering works as expected with content render array.
 */
final class ContentRenderArrayRenderTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * Tests that render works as expected.
   */
  public function testRender(): void {
    $root = $this->setupFakeSourceDir();
    $content_source_file_finder = $this->container->get('druki_content.finder.content_source_file');
    $content_source_file_list = $content_source_file_finder->findAll($root->url());
    $content_source_file = $content_source_file_list->getIterator()->offsetGet(1);
    $content_source_file_parser = $this->container->get('druki_content.parser.content_source_file');
    $content_document = $content_source_file_parser->parse($content_source_file);
    $content_render_array_builder = $this->container->get('druki_content.builder.content_render_array');
    $content_render_array = $content_render_array_builder->build($content_document->getContent());
    $renderer = $this->container->get('renderer');
    $this->container->get('twig')->disableDebug();
    $html = (string) $renderer->renderRoot($content_render_array);
    $this->container->get('twig')->enableDebug();
    $expected = \file_get_contents(__DIR__ . '/../../../fixtures/source-content.html');
    $this->assertEquals($expected, $html);
  }

}
