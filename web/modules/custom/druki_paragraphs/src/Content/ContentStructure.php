<?php

namespace Drupal\druki_paragraphs\Content;

use UnexpectedValueException;

/**
 * Class Content.
 *
 * The main object contains all necessary information for content.
 *
 * @package Drupal\druki_paragraphs\ContentStructure
 */
final class ContentStructure {

  /**
   * The meta information for this content.
   *
   * @var \Drupal\druki_paragraphs\Content\MetaInformation
   */
  protected $metaInformation;

  /**
   * The array containing content.
   *
   * @var array
   */
  protected $content = [];

  /**
   * Adds meta information to content.
   *
   * @param \Drupal\druki_paragraphs\Content\MetaInformation $meta_information
   *   The meta information.
   *
   * @return \Drupal\druki_paragraphs\Content\ContentStructure
   *   The current instance of content.
   */
  public function addMetaInformation(MetaInformation $meta_information): ContentStructure {
    if ($this->metaInformation instanceof MetaInformation) {
      throw new UnexpectedValueException('Meta information is already added. You can only add it once.');
    }

    $this->metaInformation = $meta_information;

    return $this;
  }

  /**
   * Gets last element of content.
   *
   * @return \Drupal\druki_paragraphs\Content\ParagraphContentInterface
   *   The content element instance.
   */
  public function lastContent(): ?ParagraphContentInterface {
    $last_element = end($this->content);

    if ($last_element instanceof ParagraphContentInterface) {
      return $last_element;
    }
    else {
      return NULL;
    }
  }

  /**
   * Replaces last element with new.
   *
   * @param \Drupal\druki_paragraphs\Content\ParagraphContentInterface $content_item
   *   The new content element.
   *
   * @return \Drupal\druki_paragraphs\Content\ContentStructure
   *   The current instance of content.
   */
  public function replaceLastContent(ParagraphContentInterface $content_item): ContentStructure {
    array_pop($this->content);

    return $this->addContent($content_item);
  }

  /**
   * @param \Drupal\druki_paragraphs\Content\ParagraphContentInterface $content_item
   *
   * @return \Drupal\druki_paragraphs\Content\ContentStructure
   */
  public function addContent(ParagraphContentInterface $content_item): ContentStructure {
    $this->content[] = $content_item;

    return $this;
  }

  /**
   * Gets valid status.
   *
   * The "valid" content structure is that contains meta info and content.
   *
   * @return bool
   *   The status of validation.
   */
  public function isValid(): bool {
    $is_has_meta_info = $this->getMetaInformation() instanceof MetaInformation;
    $is_has_content = count($this->getContent()) ? TRUE : FALSE;

    return $is_has_content && $is_has_meta_info;
  }

  /**
   * Gets meta information for content.
   *
   * @return \Drupal\druki_paragraphs\Content\MetaInformation
   *   The meta information, NULL if not set.
   */
  public function getMetaInformation(): ?MetaInformation {
    return $this->metaInformation;
  }

  /**
   * Gets content.
   *
   * @return \Drupal\druki_paragraphs\Content\ParagraphContentInterface[]
   *   The array contains content structured value objects.
   */
  public function getContent(): array {
    return $this->content;
  }

}
