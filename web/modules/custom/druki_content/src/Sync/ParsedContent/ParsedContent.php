<?php

namespace Drupal\druki_content\Sync\ParsedContent;

use Drupal\druki_content\Sync\ParsedContent\Content\ContentList;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface;

/**
 * Provides value object for content structure.
 *
 * @depredated Deprecated in favor of Content.
 */
final class ParsedContent {

  /**
   * The meta information for this content.
   */
  protected FrontMatterInterface $frontMatter;

  /**
   * The list of content.
   */
  protected ContentList $content;

  /**
   * ContentStructure constructor.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface $front_matter
   *   The content front matter.
   * @param \Drupal\druki_content\Sync\ParsedContent\Content\ContentList $content
   *   The content list.
   */
  public function __construct(FrontMatterInterface $front_matter, ContentList $content) {
    $this->frontMatter = $front_matter;
    $this->content = $content;
  }

  /**
   * Gets valid status for object.
   *
   * The valid content structure is that contains valid meta information and
   * one or more content items.
   *
   * @return bool
   *   TRUE if valid, FALSE otherwise.
   */
  public function valid(): bool {
    return $this->frontMatter->valid() && $this->content->count() > 0;
  }

  /**
   * Gets meta information.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface
   *   The meta information.
   */
  public function getFrontMatter(): FrontMatterInterface {
    return $this->frontMatter;
  }

  /**
   * Gets content.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\Content\ContentList
   *   The content list.
   */
  public function getContent(): ContentList {
    return $this->content;
  }

}
