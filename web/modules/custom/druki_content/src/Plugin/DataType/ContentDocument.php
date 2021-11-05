<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\StringData;
use Drupal\druki_content\Data\ContentDocument as ContentDocumentData;

/**
 * Provides content document typed data.
 *
 * @DataType(
 *   id = "druki_content_document",
 *   label = @Translation("Druki content document")
 * )
 */
final class ContentDocument extends StringData {

  /**
   * Gets content document.
   *
   * @return \Drupal\druki_content\Data\ContentDocument|null
   *   The content document object.
   */
  public function getContentDocument(): ?ContentDocumentData {
    if (empty($this->value)) {
      return NULL;
    }
    return \unserialize($this->value);
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE): void {
    if ($value instanceof ContentDocumentData) {
      $this->setContentDocument($value, $notify);
    }
    else {
      parent::setValue($value, $notify);
    }
  }

  /**
   * Sets content document value.
   *
   * @param \Drupal\druki_content\Data\ContentDocument $document
   *   The content document.
   * @param bool $notify
   *   Indicates should parent be notified.
   */
  public function setContentDocument(ContentDocumentData $document, bool $notify = TRUE): void {
    $this->value = \serialize($document);
    // Notify the parent of any changes.
    if ($notify && isset($this->parent)) {
      $this->parent->onChange($this->name);
    }
  }

}
