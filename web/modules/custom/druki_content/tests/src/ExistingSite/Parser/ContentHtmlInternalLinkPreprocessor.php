<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\ExistingSite\Parser;

use Drupal\druki_content\Data\ContentParserContext;
use Drupal\druki_content\Data\ContentSourceFile;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content internal link HTML preprocessor.
 *
 * @coversDefaultClass \Drupal\druki_content\Parser\ContentHtmlInternalLinkPreprocessor
 */
final class ContentHtmlInternalLinkPreprocessor extends ExistingSiteBase {

  /**
   * Tests that preprocessor works as expected.
   */
  public function testPreprocess(): void {
    /** @var \Drupal\druki_content\Parser\ContentHtmlPreprocessorInterface $preprocessor */
    $preprocessor = $this->container->get('druki_content.parser.content_html_internal_link_preprocessor');
    $html = '<a href="https://example.com"></a><a href="../drupal/index.md"></a>';
    $context = new ContentParserContext();
    $preprocessed_html = $preprocessor->preprocess($html, $context);
    // Without content source file it should return same value.
    $this->assertEquals($html, $preprocessed_html);

    $content_source = new ContentSourceFile('foo/bar/drupal/index.md', '../drupal/index.md', 'ru');
    $context->setContentSourceFile($content_source);
    $preprocessed_html = $preprocessor->preprocess($html, $context);
    $expected = '<a href="https://example.com"></a><a href="../drupal/index.md" data-druki-internal-link-filepath="foo/bar/drupal/index.md"></a>';
    $this->assertEquals($expected, $preprocessed_html);
  }

}
