<?php

namespace Drupal\druki_content\SourceContent;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\ParsedContent\Content\ContentList;
use Drupal\druki_content\ParsedContent\ParsedContentLoader;

/**
 * Provides class to load parsed content into "druki_content" entity.
 *
 * This class will take all care about finding the existed entity or creating
 * new one, as wel as processing all necessary values.
 */
final class ParsedSourceContentLoader {

  /**
   * The druki content storage.
   *
   * @var \Drupal\druki_content\Entity\Handler\DrukiContentStorage
   */
  protected $drukiContentStorage;

  /**
   * The system time.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The paragraph storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $paragraphStorage;

  /**
   * The parsed content loader.
   *
   * @var \Drupal\druki_content\ParsedContent\ParsedContentLoader
   */
  protected $parsedContentLoader;

  /**
   * Constructs a new ParsedSourceContentLoader object.
   *
   * @param \Drupal\druki_content\ParsedContent\ParsedContentLoader $parsed_content_loader
   *   The parsed content loader.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The system time.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(ParsedContentLoader $parsed_content_loader, EntityTypeManagerInterface $entity_type_manager, TimeInterface $time) {
    $this->parsedContentLoader = $parsed_content_loader;
    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
    $this->paragraphStorage = $entity_type_manager->getStorage('paragraph');
    $this->time = $time;
  }

  /**
   * Process parsed content.
   *
   * @param \Drupal\druki_content\SourceContent\ParsedSourceContent $parsed_source_content
   *   The parsed source content.
   * @param bool $force
   *   TRUE if content must be updated eve if source hash is equal.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The update content entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function process(ParsedSourceContent $parsed_source_content, bool $force = FALSE): DrukiContentInterface {
    $druki_content = $this->loadDrukiContent($parsed_source_content);
    $current_source_hash = $parsed_source_content->getSourceHash();
    $same_source_hash = $druki_content->getSourceHash() == $current_source_hash;
    // Update content if source_hash are different or force param set to TRUE.
    if (!$same_source_hash || $force) {
      $parsed_source = $parsed_source_content->getParsedSource();
      $druki_content->setRelativePathname($parsed_source_content->getSource()->getRelativePathname());
      $druki_content->setSourceHash($parsed_source_content->getSourceHash());

      $this->parsedContentLoader->process($parsed_source->getFrontMatter(), $druki_content);
      $this->processContent($druki_content, $parsed_source->getContent());
    }

    $druki_content->setSyncTimestamp($this->time->getCurrentTime());
    $druki_content->save();

    return $druki_content;
  }

  /**
   * Loads druki_content entity to work with.
   *
   * If entity is not found, new one will be created.
   *
   * @param \Drupal\druki_content\SourceContent\ParsedSourceContent $parsed_source_content
   *   The parsed source content.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The druki content entity.
   */
  protected function loadDrukiContent(ParsedSourceContent $parsed_source_content): DrukiContentInterface {
    $front_matter = $parsed_source_content->getParsedSource()->getFrontMatter();
    $id = $front_matter->get('id')->getValue();
    $core = NULL;
    if ($front_matter->has('core')) {
      $core = $front_matter->get('core')->getValue();
    }
    $language = $parsed_source_content->getSource()->getLanguage();
    $entity = $this->drukiContentStorage->loadByExternalId($id, $language, $core);

    if (!$entity) {
      $entity = $this->drukiContentStorage->create([
        'external_id' => $id,
        'langcode' => $language,
      ]);
    }

    return $entity;
  }

  /**
   * Process content values.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   * @param \Drupal\druki_content\ParsedContent\Content\ContentList $content_list
   *   The parsed content from source.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function processContent(DrukiContentInterface $druki_content, ContentList $content_list): void {
    $this->deleteParagraphs($druki_content);
    $this->createParagraphs($druki_content, $content_list);
  }

  /**
   * Delete all paragraphs with content.
   *
   * If this content already contains paragraphs, we delete them. It's faster
   * and safer to recreate it from new structure, other than detecting
   * changes. Maybe in the future it will be improved, but not in experiment.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function deleteParagraphs(DrukiContentInterface $druki_content): void {
    if ($druki_content->get('content')->isEmpty()) {
      return;
    }

    $paragraphs = $druki_content->get('content')->referencedEntities();
    $this->paragraphStorage->delete($paragraphs);

    $druki_content->set('content', NULL);
  }

  /**
   * Creates paragraphs for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   * @param \Drupal\druki_content\ParsedContent\Content\ContentList $content_list
   *   The parsed content from source.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphs(DrukiContentInterface $druki_content, ContentList $content_list): void {
    /** @var \Drupal\druki_content\ParsedContent\Content\ParagraphContentInterface $content */
    foreach ($content_list as $content) {
      $this->parsedContentLoader->process($content, $druki_content);
    }
  }

}
