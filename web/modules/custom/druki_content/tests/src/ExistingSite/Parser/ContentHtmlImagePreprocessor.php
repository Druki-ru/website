<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\ExistingSite\Parser;

use Drupal\druki_content\Data\ContentParserContext;
use Drupal\druki_content\Data\ContentSourceFile;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content image HTML preprocessor.
 *
 * @coversDefaultClass \Drupal\druki_content\Parser\ContentHtmlImagePreprocessor
 */
final class ContentHtmlImagePreprocessor extends ExistingSiteBase {

  /**
   * Tests that preprocessor works as expected.
   */
  public function testPreprocess(): void {
    /** @var \Drupal\druki_content\Parser\ContentHtmlPreprocessorInterface $preprocessor */
    $preprocessor = $this->container->get('druki_content.parser.content_html_image_preprocessor');
    $html = <<<'HTML'
      <img src="https://example.com/kitty.jpg">
      <img src="./logo.png">
      <img src="logo.png">
      <img src="../drupal/logo.png">
    HTML;
    $context = new ContentParserContext();
    $preprocessed_html = $preprocessor->preprocess($html, $context);
    // Without content source file it should return same value.
    $this->assertEquals($html, $preprocessed_html);

    $content_source = new ContentSourceFile('foo/bar/drupal/index.md', '../drupal/index.md', 'ru');
    $context->setContentSourceFile($content_source);
    $preprocessed_html = $preprocessor->preprocess($html, $context);
    $expected = <<<'HTML'
    <img src="https://example.com/kitty.jpg">
      <img src="druki-content-source://foo/bar/drupal/./logo.png">
      <img src="druki-content-source://foo/bar/drupal/logo.png">
      <img src="druki-content-source://foo/bar/drupal/../drupal/logo.png">
    HTML;
    $this->assertEquals($expected, $preprocessed_html);
  }

}
