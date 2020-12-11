<?php

namespace Drupal\druki_content\Sync\SourceContent;

use Drupal\Component\Utility\Crypt;
use Drupal\druki_content\Sync\ParsedContent\ParsedContent;

/**
 * Provides value object for parsed source content.
 */
final class ParsedSourceContent {

  /**
   * The source content.
   *
   * @var \Drupal\druki_content\Sync\SourceContent\SourceContent
   */
  protected $source;

  /**
   * The parsed content.
   *
   * @var \Drupal\druki_content\Sync\ParsedContent\ParsedContent
   */
  protected $parsed;

  /**
   * Constructs a new ParsedSourceContent object.
   *
   * @param \Drupal\druki_content\Sync\SourceContent\SourceContent $source_content
   *   The source content.
   * @param \Drupal\druki_content\Sync\ParsedContent\ParsedContent $parsed_content
   *   The parsed content.
   */
  public function __construct(SourceContent $source_content, ParsedContent $parsed_content) {
    $this->source = $source_content;
    $this->parsed = $parsed_content;
  }

  /**
   * Gets source content.
   *
   * @return \Drupal\druki_content\Sync\SourceContent\SourceContent
   *   The source content.
   */
  public function getSource(): SourceContent {
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
