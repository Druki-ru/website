<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\Component\Utility\UrlHelper;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides HTML image preprocessor.
 *
 * If source content uses relative paths for images, that means that image is
 * part of source content repository. To help further processing of this
 * information, the relative path is replaced by internal uri. For example,
 *
 * @code
 *   <img src="content-source://drupal/logo.png" alt="Drupal Logo!">
 * @endcode
 *
 * This helps to understand where is that image and avoid hold where is that
 * path actually and what it related to.
 */
final class ContentHtmlImagePreprocessor implements ContentHtmlPreprocessorInterface {

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
    $images = $crawler->filter('img');
    /** @var \DOMElement $image */
    foreach ($images as $image) {
      $src = $image->getAttribute('src');
      // The external images doesn't need any processing.
      if (UrlHelper::isExternal($src)) {
        continue;
      }
      $src = \ltrim($src, DIRECTORY_SEPARATOR);
      $uri_parts = [
        'content-source:/',
        \pathinfo($source_content_file->getRealpath(), \PATHINFO_DIRNAME),
        $src,
      ];
      $image->setAttribute('src', \implode(DIRECTORY_SEPARATOR, $uri_parts));
    }
    return $crawler->filter('body')->html();
  }

}
