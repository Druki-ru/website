<?php

namespace Drupal\druki_content\Commands;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\druki_content\Sync\Queue\QueueProcessorInterface;
use Drupal\druki_content\Sync\SourceContent\SourceContent;
use Drupal\druki_content\Sync\SourceContent\SourceContentList;
use Drupal\druki_content\Sync\SourceContent\SourceContentListQueueItem;
use Drupal\druki_git\Git\GitInterface;
use Drush\Commands\DrushCommands;

/**
 * Provides drush commands for synchronization "druki_content" entity.
 */
class DrukiContentSyncCommands extends DrushCommands {

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
  protected QueueProcessorInterface $queueProcessor;

  /**
   * DrukiContentSyncCommands constructor.
   *
   * @param \Drupal\druki_git\Git\GitInterface $gitService
   *   The git service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager service.
   * @param \Drupal\druki_content\Sync\Queue\QueueProcessorInterface $queueProcessor
   *   The queue item processors.
   */
  public function __construct(GitInterface $gitService, LanguageManagerInterface $languageManager, QueueProcessorInterface $queueProcessor) {
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
    $sourceContent = new SourceContent($realPath, $uri, $locale);
    $sourceContentList = (new SourceContentList())->add($sourceContent);
    $this->queueProcessor->process(new SourceContentListQueueItem($sourceContentList));
  }

}
