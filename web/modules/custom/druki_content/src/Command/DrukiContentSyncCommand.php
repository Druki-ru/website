<?php

namespace Drupal\druki_content\Command;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\druki\Queue\ChainEntitySyncQueueItemProcessorInterface;
use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\druki_content\Data\ContentSourceFileList;
use Drupal\druki_content\Data\ContentSourceFileListQueueItem;
use Drupal\druki_content\Repository\ContentSourceSettingsInterface;
use Drush\Commands\DrushCommands;

/**
 * Provides drush commands for synchronization "druki_content" entity.
 */
final class DrukiContentSyncCommand extends DrushCommands {

  /**
   * The language manager service.
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * The queue item processors.
   */
  protected ChainEntitySyncQueueItemProcessorInterface $queueProcessor;

  /**
   * The content source settings.
   */
  protected ContentSourceSettingsInterface $contentSourceSettings;

  /**
   * Constructs a new DrukiContentSyncCommand object.
   *
   * @param \Drupal\druki_content\Repository\ContentSourceSettingsInterface $content_source_settings
   *   The content source settings.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\druki\Queue\ChainEntitySyncQueueItemProcessorInterface $queue_processor
   *   The queue processor.
   */
  public function __construct(ContentSourceSettingsInterface $content_source_settings, LanguageManagerInterface $language_manager, ChainEntitySyncQueueItemProcessorInterface $queue_processor) {
    parent::__construct();
    $this->contentSourceSettings = $content_source_settings;
    $this->languageManager = $language_manager;
    $this->queueProcessor = $queue_processor;
  }

  /**
   * Synchronization one entity by source file.
   *
   * @param string $uri
   *   The URI to source file.
   * @param array $options
   *   An associative array of options.
   *
   * @option locale A short language code.
   * @command druki-content:sync-file
   *
   * @usage drush druki-content:sync-file docs/ru/drupal/9/routing/index.md --locale=ru
   *   Create/Update entity URI to source file.
   */
  public function syncFile(string $uri, array $options = ['locale' => NULL]): void {
    $realpath = \rtrim($this->contentSourceSettings->getRepositoryUri(), "/");
    $realpath .= '/' . \ltrim($uri, "/");
    $locale = $options['locale'];
    if (empty($locale)) {
      $default_language = $this->languageManager->getDefaultLanguage();
      $locale = $default_language->getId();
    }
    $content_source_file = new ContentSourceFile($realpath, $uri, $locale);
    $content_source_file_list = (new ContentSourceFileList())->addFile($content_source_file);
    $this->queueProcessor->process(new ContentSourceFileListQueueItem($content_source_file_list));
  }

}
