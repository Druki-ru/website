<?php

namespace Drupal\druki_content\Command;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\druki_content\Data\ContentSourceFileList;
use Drupal\druki_content\Data\ContentSourceFileListQueueItem;
use Drupal\druki_content\Queue\ContentSyncQueueProcessorInterface;
use Drupal\druki_git\Git\GitInterface;
use Drush\Commands\DrushCommands;

/**
 * Provides drush commands for synchronization "druki_content" entity.
 */
class DrukiContentSyncCommand extends DrushCommands {

  /**
   * The git service.
   */
  protected GitInterface $gitService;

  /**
   * The language manager service.
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * The queue item processors.
   */
  protected ContentSyncQueueProcessorInterface $queueProcessor;

  /**
   * DrukiContentSyncCommands constructor.
   *
   * @param \Drupal\druki_git\Git\GitInterface $gitService
   *   The git service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager service.
   * @param \Drupal\druki_content\Queue\ContentSyncQueueProcessorInterface $queueProcessor
   *   The queue item processors.
   */
  public function __construct(GitInterface $gitService, LanguageManagerInterface $languageManager, ContentSyncQueueProcessorInterface $queueProcessor) {
    parent::__construct();
    $this->gitService = $gitService;
    $this->languageManager = $languageManager;
    $this->queueProcessor = $queueProcessor;
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
    $realPath = \rtrim($this->gitService->getRepositoryRealpath(), "/");
    $realPath .= '/' . \ltrim($uri, "/");
    $locale = $options['locale'];
    if (empty($locale)) {
      $activeLanguage = $this->languageManager->getDefaultLanguage();
      $locale = $activeLanguage->getId();
    }
    $sourceContent = new ContentSourceFile($realPath, $uri, $locale);
    $sourceContentList = (new ContentSourceFileList())->addFile($sourceContent);
    $this->queueProcessor->process(new ContentSourceFileListQueueItem($sourceContentList));
  }

}
