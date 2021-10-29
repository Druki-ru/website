<?php

declare(strict_types=1);

namespace Drupal\druki_content\Generator;

use Drupal\druki_content\Data\ContentDocument;

/**
 * Provides content document checksum generator.
 *
 * The main purpose of checksum is to avoid updating content entities if content
 * document, from which they built, is not changed during update process.
 */
final class ContentDocumentChecksumGenerator {

  /**
   * The generator version.
   *
   * The version is needed for cases when something is changed but this will not
   * reflects in checksum change. E.g. we changed logic for processing
   * ContentDocument but the value remains the same. In that case checksum will
   * be the same, but since the logic of processing that object is changed, it
   * will creates different result.
   *
   * By changing this version, the processor will see the old checksum, with
   * previouse version and the new one, and they will be different. In that case
   * ContentDocument will be processed even if it's not changed.
   */
  protected const VERSION = '1.0';

  /**
   * Generates checksum for given content document.
   *
   * @param \Drupal\druki_content\Data\ContentDocument $content_document
   *   The content document.
   *
   * @return string
   *   The checksum.
   */
  public function generate(ContentDocument $content_document): string {
    $checksum_parts = [
      self::VERSION,
      \serialize($content_document),
    ];
    return \md5(\implode(':', $checksum_parts));
  }

}
