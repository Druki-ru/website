<?php

namespace Drupal\druki_content\Synchronization\Content;

use Drupal\druki_content\Synchronization\MetaInformation\MetaInformation;

/**
 * Provides value object for content structure.
 */
final class ContentStructure {

  /**
   * The meta information for this content.
   *
   * @var \Drupal\druki_content\Synchronization\MetaInformation\MetaInformation
   */
  protected $metaInformation;

  /**
   * The list of content.
   *
   * @var \Drupal\druki_content\Synchronization\Content\ContentList
   */
  protected $content;

  /**
   * ContentStructure constructor.
   *
   * @param \Drupal\druki_content\Synchronization\MetaInformation\MetaInformation $meta_information
   *   The content meta information.
   * @param \Drupal\druki_content\Synchronization\Content\ContentList $content
   *   The content list.
   */
  public function __construct(MetaInformation $meta_information, ContentList $content) {
    $this->metaInformation = $meta_information;
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
    return $this->metaInformation->valid() && $this->content->count() > 0;
  }

  /**
   * Gets meta information.
   *
   * @return \Drupal\druki_content\Synchronization\MetaInformation\MetaInformation
   *   The meta information.
   */
  public function getMetaInformation(): MetaInformation {
    return $this->metaInformation;
  }

  /**
   * Gets content.
   *
   * @return \Drupal\druki_content\Synchronization\Content\ContentList
   *   The content list.
   */
  public function getContent(): ContentList {
    return $this->content;
  }

}
