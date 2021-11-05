<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides HTML internal link preprocessor.
 *
 * The content in source uses relative links to other documents. E.g.:
 *
 * @code
 * <a href="/drupal/about/index.md">Link to content B</a>
 * @endcode
 *
 * This preprocessor adds special data attribute to such links with additional
 * information. This information will help to convert their links into actual
 * links to generated content. This is done in such a way, because it is
 * possible for content A have link to content B, because it imports later. By
 * this additional info we will add special cache tags to content A, and when
 * content B will be created, the link can be generated properly.
 *
 * @see \Drupal\druki_content\Plugin\Filter\InternalLinks
 */
final class ContentHtmlInternalLinkPreprocessor implements ContentHtmlPreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(string $html, ContentParserContext $context): string {
    $source_content_file = $context->getContentSourceFile();
    // Without such information we cannot process.
    if (!$source_content_file) {
      return $html;
    }

    $crawler = new Crawler($html);
    $links = $crawler->filter('a[href$=".md"],a[href$=".MD"]');
    /** @var \DOMElement $link */
    foreach ($links as $link) {
      $link->setAttribute('data-druki-internal-link-filepath', $source_content_file->getRealpath());
    }
    return $crawler->filter('body')->html();
  }

}
