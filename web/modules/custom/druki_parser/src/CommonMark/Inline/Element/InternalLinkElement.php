<?php

namespace Drupal\druki_parser\CommonMark\Inline\Element;

use League\CommonMark\Inline\Element\AbstractInlineContainer;

/**
 * Class InternalLinksElement
 *
 * @package Drupal\druki_parser\CommonMark\Inline\Element
 */
class InternalLinkElement extends AbstractInlineContainer {

  /**
   * The content ID.
   *
   * @var string
   */
  protected $contentId;

  /**
   * The title.
   *
   * @var string
   */
  protected $title;

  public function __construct($content_id, $title) {
    $this->contentId = $content_id;
    $this->title = $title;
  }

  /**
   * Gets content ID.
   *
   * @return string
   *   The content ID.
   */
  public function getContentId() {
    return $this->contentId;
  }

  /**
   * Sets content ID.
   *
   * @param string $content_id
   *   The content ID.
   */
  public function setContentId(string $content_id) {
    $this->contentId = $content_id;
  }

  /**
   * Gets title.
   *
   * @return string
   *   The title.
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Sets title.
   *
   * @param string $title
   *   The title.
   */
  public function setTitle(string $title) {
    $this->title = $title;
  }

}
