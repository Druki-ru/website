<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\Sync\ParsedContent\ParsedContentItemLoaderBase;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Provides base class for paragraph entity type content loaders.
 */
abstract class ParagraphLoaderBase extends ParsedContentItemLoaderBase {

  /**
   * The paragraph storage.
   */
  protected EntityStorageInterface $paragraphStorage;

  /**
   * The default text filter for text fields.
   *
   * Use full html for default form since we convert markdown during sync. Using
   * markdown filter will only reduce performance for nothing.
   */
  protected string $defaultTextFilter = 'full_html';

  /**
   * Constructs a new ParagraphLoaderBase object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->paragraphStorage = $entity_type_manager->getStorage('paragraph');
  }

  /**
   * Gets default text filter.
   *
   * @return string
   *   The default text filter.
   */
  public function getDefaultTextFilter(): string {
    return $this->defaultTextFilter;
  }

  /**
   * Gets paragraph storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The paragraph storage.
   */
  protected function getParagraphStorage(): EntityStorageInterface {
    return $this->paragraphStorage;
  }

  /**
   * Save and appends paragraph to content.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The paragraph to append.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The content to append to.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function saveAndAppend(ParagraphInterface $paragraph, DrukiContentInterface $content): void {
    $paragraph->save();
    $content->get('content')->appendItem([
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ]);
  }

}
