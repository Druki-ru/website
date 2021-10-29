<?php

declare(strict_types=1);

namespace Drupal\druki_content\Queue;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\druki_content\Data\ContentSourceFileListQueueItem;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\Generator\ContentDocumentChecksumGenerator;
use Drupal\druki_content\Parser\ContentSourceFileParser;
use Drupal\druki_content\Repository\DrukiContentStorage;

/**
 * Provides queue item processor for content source file list.
 */
final class ContentSourceFileListQueueItemProcessor implements ContentSyncQueueProcessorInterface {

  /**
   * The druki content storage.
   */
  protected DrukiContentStorage $contentStorage;

  /**
   * The content source file parser.
   */
  protected ContentSourceFileParser $contentSourceFileParser;

  /**
   * The content document checksum generator.
   */
  protected ContentDocumentChecksumGenerator $checksumGenerator;

  /**
   * Constructs a new ContentSourceFileListQueueItemProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\druki_content\Parser\ContentSourceFileParser $content_source_file_parser
   *   The content source file parser.
   * @param \Drupal\druki_content\Generator\ContentDocumentChecksumGenerator $checksum_generator
   *   The checksum generator.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ContentSourceFileParser $content_source_file_parser, ContentDocumentChecksumGenerator $checksum_generator) {
    $this->contentStorage = $entity_type_manager->getStorage('druki_content');
    $this->contentSourceFileParser = $content_source_file_parser;
    $this->checksumGenerator = $checksum_generator;
  }

  /**
   * {@inheritdoc}
   */
  public function process(ContentSyncQueueItemInterface $item): void {
    \assert($item instanceof ContentSourceFileListQueueItem);
    $content_source_file_list = $item->getPayload();
    /** @var \Drupal\druki_content\Data\ContentSourceFile $content_source_file */
    foreach ($content_source_file_list->getIterator() as $content_source_file) {
      $this->processContentSourceFile($content_source_file);
    }
  }

  /**
   * Processes single content source file.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFile $content_source_file
   *   The content source file.
   */
  protected function processContentSourceFile(ContentSourceFile $content_source_file): void {
    $content_document = $this->contentSourceFileParser->parse($content_source_file);
    $content_metadata = $content_document->getMetadata();
    $content_entity = $this->prepareContentEntity($content_document);

    $destination_checksum = $content_entity->getSourceHash();
    $source_checksum = $this->checksumGenerator->generate($content_document);
    if ($destination_checksum == $source_checksum) {
      return;
    }
    $content_entity->setSourceHash($source_checksum);

    $content_entity->setTitle($content_metadata->getTitle());
    $content_entity->setRelativePathname($content_source_file->getRelativePathname());
    $content_entity->setContentDocument($content_document);

    $content_entity->setCore(NULL);
    if ($core = $content_metadata->getCore()) {
      $content_entity->setCore($core);
    }

    $content_entity->unsetCategory();
    if ($category = $content_metadata->getCategory()) {
      $content_entity->setCategory($category['area'], $category['order'], $category['title']);
    }

    if ($content_entity->hasField('search_keywords')) {
      $content_entity->set('search_keywords', NULL);
      if ($content_metadata->hasSearchKeywords()) {
        $content_entity->set('search_keywords', $content_metadata->getSearchKeywords());
      }
    }

    if ($content_entity->hasField('metatags')) {
      $content_entity->set('metatags', NULL);
      if ($content_metadata->hasMetatags()) {
        $content_entity->set('metatags', \serialize($content_metadata->getMetatags()));
      }
    }

    $content_entity->save();
  }

  /**
   * Loads or creates content entity.
   *
   * @param \Drupal\druki_content\Data\ContentDocument $content_document
   *   The content document.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The content entity.
   */
  protected function prepareContentEntity(ContentDocument $content_document): DrukiContentInterface {
    $content = $this->contentStorage->loadBySlug(
      $content_document->getMetadata()->getSlug(),
      $content_document->getLanguage(),
    );
    if ($content) {
      return $content;
    }
    return $this->contentStorage->create([
      'langcode' => $content_document->getLanguage(),
      'slug' => $content_document->getMetadata()->getSlug(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(ContentSyncQueueItemInterface $item): bool {
    return $item instanceof ContentSourceFileListQueueItem;
  }

}
