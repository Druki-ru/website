<?php

namespace Drupal\druki_content\Sync;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\druki_content\SourceContent\ParsedSourceContentLoader;
use Drupal\druki_content\SourceContent\SourceContent;
use Drupal\druki_content\SourceContent\SourceContentList;
use Drupal\druki_content\SourceContent\SourceContentParser;

/**
 * Provides processing for sync queue items.
 */
final class SyncQueueProcessor {

  /**
   * The druki content storage.
   *
   * @var \Drupal\druki_content\Handler\DrukiContentStorage
   */
  protected $drukiContentStorage;

  /**
   * The source content parser.
   *
   * @var \Drupal\druki_content\SourceContent\SourceContentParser
   */
  protected $sourceContentParser;

  /**
   * The parsed content loader.
   *
   * @var \Drupal\druki_content\SourceContent\ParsedSourceContentLoader
   */
  protected $parsedSourceContentLoader;

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a new SyncQueueProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\druki_content\SourceContent\SourceContentParser $source_content_parser
   *   The source content parser.
   * @param \Drupal\druki_content\SourceContent\ParsedSourceContentLoader $parsed_content_loader
   *   The parsed content loader.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, SourceContentParser $source_content_parser, ParsedSourceContentLoader $parsed_content_loader, StateInterface $state) {
    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
    $this->sourceContentParser = $source_content_parser;
    $this->parsedSourceContentLoader = $parsed_content_loader;
    $this->state = $state;
  }

  /**
   * Process single queue item.
   *
   * @param \Drupal\druki_content\Sync\SyncQueueItem $queue_item
   *   The queue item to process.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function processItem(SyncQueueItem $queue_item): void {
    switch ($queue_item->getOperation()) {
      case SyncQueueItem::SYNC:
        $this->processSync($queue_item->getPayload());
        break;

      case SyncQueueItem::CLEAN:
        $this->processClean($queue_item->getPayload());
        break;
    }
  }

  /**
   * Process synchronization queue item.
   *
   * This processor will create or update content for provided payload.
   *
   * @param \Drupal\druki_content\SourceContent\SourceContentList $source_content_list
   *   The list content to process.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function processSync(SourceContentList $source_content_list): void {
    $force_update = $this->state->get('druki_content.settings.force_update', FALSE);
    foreach ($source_content_list as $source_content) {
      $this->processSourceContent($source_content, $force_update);
    }
  }

  /**
   * Process single source content item.
   *
   * @param \Drupal\druki_content\SourceContent\SourceContent $source_content
   *   The source content.
   * @param bool $force
   *   TRUE will force sync even if content is not changed.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function processSourceContent(SourceContent $source_content, bool $force): void {
    $parsed_source_content = $this->sourceContentParser->parse($source_content);
    $this->parsedSourceContentLoader->process($parsed_source_content, $force);
  }

  /**
   * Process clean up.
   *
   * This processor will remove all content which has last sync timestamp lesser
   * than queue was built.
   *
   * Since this item will be always at the end, all content must be processed
   * at this moment.
   *
   * @param int $timestamp
   *   The queue creation timestamp.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function processClean(int $timestamp): void {
    $this->drukiContentStorage->cleanOutdated($timestamp);
  }

}
