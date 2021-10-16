<?php

namespace Drupal\druki_content\Sync\SourceContent;

use Drupal\Component\Utility\Crypt;
use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\druki_content\Sync\ParsedContent\ParsedContent;

/**
 * Provides value object for parsed source content.
 *
 * @todo Refactor to ContentDocument.
 */
final class ParsedSourceContent {

  /**
   * The source content.
   */
  protected ContentSourceFile $source;

  /**
   * The parsed content.
   */
  protected ParsedContent $parsed;

  /**
   * Constructs a new ParsedSourceContent object.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFile $source_content
   *   The source content.
   * @param \Drupal\druki_content\Sync\ParsedContent\ParsedContent $parsed_content
   *   The parsed content.
   */
  public function __construct(ContentSourceFile $source_content, ParsedContent $parsed_content) {
    $this->source = $source_content;
    $this->parsed = $parsed_content;
  }

  /**
   * Gets source content.
   *
   * @return \Drupal\druki_content\Data\ContentSourceFile
   *   The source content.
   */
  public function getSource(): ContentSourceFile {
    return $this->source;
  }

  /**
   * Gets parsed source content.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\ParsedContent
   *   THe parsed source content.
   */
  public function getParsedSource(): ParsedContent {
    return $this->parsed;
  }

  /**
   * Gets the parsed source content hash.
   *
   * This used for determine that content not changed.
   *
   * @return string
   *   The hash for source and parsed source content combined.
   */
  public function getSourceHash(): string {
    $string = \serialize($this->source) . \serialize($this->parsed);
    return Crypt::hashBase64($string);
  }

}
