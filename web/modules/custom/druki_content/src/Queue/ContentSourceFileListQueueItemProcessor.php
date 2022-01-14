<?php

declare(strict_types=1);

namespace Drupal\druki_content\Queue;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki\Parser\GitOutputParser;
use Drupal\druki\Process\GitInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface;
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
final class ContentSourceFileListQueueItemProcessor implements EntitySyncQueueItemProcessorInterface {

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
   * The git process.
   */
  protected GitInterface $git;

  /**
   * Constructs a new ContentSourceFileListQueueItemProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\druki_content\Parser\ContentSourceFileParser $content_source_file_parser
   *   The content source file parser.
   * @param \Drupal\druki_content\Generator\ContentDocumentChecksumGenerator $checksum_generator
   *   The checksum generator.
   * @param \Drupal\druki\Process\GitInterface $git
   *   The git process.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ContentSourceFileParser $content_source_file_parser, ContentDocumentChecksumGenerator $checksum_generator, GitInterface $git) {
    $this->contentStorage = $entity_type_manager->getStorage('druki_content');
    $this->contentSourceFileParser = $content_source_file_parser;
    $this->checksumGenerator = $checksum_generator;
    $this->git = $git;
  }

  /**
   * {@inheritdoc}
   */
  public function process(EntitySyncQueueItemInterface $item): array {
    \assert($item instanceof ContentSourceFileListQueueItem);
    $content_source_file_list = $item->getPayload();
    $ids = [];
    /** @var \Drupal\druki_content\Data\ContentSourceFile $content_source_file */
    foreach ($content_source_file_list->getIterator() as $content_source_file) {
      $ids[] = $this->processContentSourceFile($content_source_file);
    }
    return $ids;
  }

  /**
   * Processes single content source file.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFile $content_source_file
   *   The content source file.
   *
   * @return int
   *   The ID of created or updated content entity.
   */
  protected function processContentSourceFile(ContentSourceFile $content_source_file): int {
    $content_document = $this->contentSourceFileParser->parse($content_source_file);
    $content_metadata = $content_document->getMetadata();
    $content_entity = $this->prepareContentEntity($content_document);

    $destination_checksum = $content_entity->getSourceHash();
    $source_checksum = $this->checksumGenerator->generate($content_document);
    if ($destination_checksum == $source_checksum) {
      return (int) $content_entity->id();
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

    $content_entity->unsetContributors();
    // Get directory of git root. We don't need here to request settings with
    // git root, because it can be simple evaluated by removing relative
    // pathname from realpath.
    //
    // E.g.:
    // - realpath: '/path/to/content/git/folder/content/index.md'.
    // - relative pathname: '/content/index.md'.
    // - $directory: '/path/to/content/git/folder'.
    $directory = \str_replace($content_source_file->getRelativePathname(), '', $content_source_file->getRealpath());
    $contributors_process = $this->git->getFileContributors($directory, $content_source_file->getRelativePathname());
    $contributors_process->run();
    if ($contributors_process->isSuccessful()) {
      $contributors = GitOutputParser::parseContributorsLog($contributors_process->getOutput());
      $content_entity->setContributors($contributors);
    }

    $content_entity->save();
    return (int) $content_entity->id();
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
      'type' => 'document',
      'langcode' => $content_document->getLanguage(),
      'slug' => $content_document->getMetadata()->getSlug(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(EntitySyncQueueItemInterface $item): bool {
    return $item instanceof ContentSourceFileListQueueItem;
  }

}
