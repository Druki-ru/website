<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileList;

/**
 * Provides processor for redirect sync queue items.
 */
final class RedirectSyncQueueProcessor {

  /**
   * The redirect storage.
   */
  protected ContentEntityStorageInterface $redirectStorage;

  /**
   * Constructs a new RedirectSyncQueueProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->redirectStorage = $entity_type_manager->getStorage('redirect');
  }

  /**
   * Process single queue item.
   *
   * @param \Drupal\druki_redirect\Data\RedirectFileList $redirect_file_list
   *   The redirect file list.
   */
  public function process(RedirectFileList $redirect_file_list): void {
    $this->cleanRedirects();
    // @todo Use \Drupal\redirect\Entity\Redirect::generateHash to reduce extra
    // processing.
    foreach ($redirect_file_list->getIterator() as $redirect_file) {
      $this->createRedirects($redirect_file);
    }
  }

  /**
   * Cleans custom created redirects.
   */
  protected function cleanRedirects(): void {
    $redirect_ids = $this->redirectStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('druki_content_redirect', TRUE)
      ->execute();
    if (empty($redirect_ids)) {
      return;
    }
    $redirects = $this->redirectStorage->loadMultiple($redirect_ids);
    $this->redirectStorage->delete($redirects);
  }

  /**
   * Creates redirects from provided file.
   *
   * @param \Drupal\druki_redirect\Data\RedirectFile $redirect_file
   *   The redirect file.
   */
  protected function createRedirects(RedirectFile $redirect_file): void {
    if (($handle = \fopen($redirect_file->getPathname(), 'r')) !== FALSE) {
      while (($row = \fgetcsv($handle)) !== FALSE) {
        $from = UrlHelper::parse($row[0]);
        $to = UrlHelper::parse($row[1]);
        $redirect = $this->redirectStorage->create();
        $redirect->setLanguage($redirect_file->getLanguage());
        $redirect->setStatusCode(301);
        $redirect->setSource($from['path'], $from['query']);
        $redirect->setRedirect($to['path'], $to['query'], ['fragment' => $to['fragment']]);
        $redirect->set('druki_content_redirect', TRUE);
        $redirect->save();
      }
    }
  }

  /**
   * Process raw path from file.
   *
   * @param string $raw_path
   *   The raw URL.
   *
   * @return array
   *   An array with processed values:
   *   - path: The url path.
   *   - query: The url query.
   */
  protected function massagePath(string $raw_path): array {
    $url = [
      'path' => $raw_path,
      'query' => NULL,
    ];
    if (\str_contains($raw_path, '?')) {
      $url = UrlHelper::parse($raw_path);
    }
    return $url;
  }

}
