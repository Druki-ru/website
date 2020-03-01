<?php

namespace Drupal\druki_content\SourceContent;

use Drupal\druki_content\ParsedContent\ParsedContent;

/**
 * Provides value object for parsed source content.
 */
final class ParsedSourceContent {

  /**
   * The source content.
   *
   * @var \Drupal\druki_content\SourceContent\SourceContent
   */
  protected $source;

  /**
   * The parsed content.
   *
   * @var \Drupal\druki_content\ParsedContent\ParsedContent
   */
  protected $parsed;

  /**
   * Constructs a new ParsedSourceContent object.
   *
   * @param \Drupal\druki_content\SourceContent\SourceContent $source_content
   *   The source content.
   * @param \Drupal\druki_content\ParsedContent\ParsedContent $parsed_content
   *   The parsed content.
   */
  public function __construct(SourceContent $source_content, ParsedContent $parsed_content) {
    $this->source = $source_content;
    $this->parsed = $parsed_content;
  }

  /**
   * Gets source content.
   *
   * @return \Drupal\druki_content\SourceContent\SourceContent
   *   The source content.
   */
  public function getSource(): SourceContent {
    return $this->source;
  }

  /**
   * Gets parsed source content.
   *
   * @return \Drupal\druki_content\ParsedContent\ParsedContent
   *   THe parsed source content.
   */
  public function getParsedSource(): ParsedContent {
    return $this->parsed;
  }

}
