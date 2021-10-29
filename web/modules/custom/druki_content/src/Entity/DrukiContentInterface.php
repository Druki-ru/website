<?php

namespace Drupal\druki_content\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\druki_content\Data\ContentDocument;

/**
 * Provides an interface defining a druki content entity type.
 */
interface DrukiContentInterface extends ContentEntityInterface {

  /**
   * Gets the druki content title.
   *
   * @return string
   *   Title of the druki content.
   */
  public function getTitle(): string;

  /**
   * Sets the druki content title.
   *
   * @param string $title
   *   The druki content title.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setTitle(string $title): DrukiContentInterface;

  /**
   * Gets relative pathname of source file.
   *
   * @return string
   *   The relative pathname.
   */
  public function getRelativePathname(): string;

  /**
   * Sets relative pathname.
   *
   * @param string $relative_pathname
   *   The relative pathname.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setRelativePathname(string $relative_pathname): DrukiContentInterface;

  /**
   * Sets core version.
   *
   * @param int|null $core
   *   The core version.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setCore(?int $core): DrukiContentInterface;

  /**
   * Gets core version.
   *
   * @return string|null
   *   The core version, NULL if not set.
   */
  public function getCore(): ?string;

  /**
   * Sets category.
   *
   * @param string $area
   *   The category area.
   * @param int $order
   *   The order inside area.
   * @param string|null $title
   *   The custom title for category.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setCategory(string $area, int $order = 0, ?string $title = NULL): DrukiContentInterface;

  /**
   * Reset category value.
   */
  public function unsetCategory(): void;

  /**
   * Gets category.
   *
   * @return array|null
   *   The category info.
   */
  public function getCategory(): ?array;

  /**
   * Gets last sync timestamp where content was found.
   *
   * @return int|null
   *   The last sync timestamp. NULL if not synced before.
   */
  public function getSyncTimestamp(): ?int;

  /**
   * Sets last synchronization timestamp for this content.
   *
   * @param int $timestamp
   *   The last sync timestamp.
   *
   * @return $this
   */
  public function setSyncTimestamp(int $timestamp): DrukiContentInterface;

  /**
   * Gets source hash.
   *
   * @return string|null
   *   The source hash.
   */
  public function getSourceHash(): ?string;

  /**
   * Sets source hash.
   *
   * @param string $hash
   *   The hash.
   *
   * @return $this
   */
  public function setSourceHash(string $hash): DrukiContentInterface;

  /**
   * Gets content slug.
   *
   * @return string
   *   The content slug.
   */
  public function getSlug(): string;

  /**
   * Sets content document.
   *
   * @param \Drupal\druki_content\Data\ContentDocument $content_document
   *   The content document.
   *
   * @return $this
   */
  public function setContentDocument(ContentDocument $content_document): self;

  /**
   * Gets content document.
   *
   * @return \Drupal\druki_content\Data\ContentDocument
   *   The content document.
   */
  public function getContentDocument(): ContentDocument;

}
