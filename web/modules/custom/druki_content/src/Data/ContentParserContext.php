<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides object to pass additional information to content parsers.
 */
final class ContentParserContext {

  /**
   * The content source file.
   */
  protected ?ContentSourceFile $contentSourceFile = NULL;

  /**
   * The structured content.
   */
  protected ?Content $content = NULL;

  /**
   * Sets content source file.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFile $content_source_file
   *   The content source file.
   */
  public function setContentSourceFile(ContentSourceFile $content_source_file): void {
    $this->contentSourceFile = $content_source_file;
  }

  /**
   * Gets content source file.
   *
   * @return \Drupal\druki_content\Data\ContentSourceFile|null
   *   The content source file.
   */
  public function getContentSourceFile(): ?ContentSourceFile {
    return $this->contentSourceFile;
  }

  /**
   * Sets structured content.
   *
   * @param \Drupal\druki_content\Data\Content $content
   *   The structured content.
   */
  public function setContent(Content $content): void {
    $this->content = $content;
  }

  /**
   * Gets structured content.
   *
   * @return \Drupal\druki_content\Data\Content|null
   *   The structured content.
   */
  public function getContent(): ?Content {
    return $this->content;
  }

}
