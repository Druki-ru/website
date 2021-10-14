<?php

namespace Drupal\druki_content\Queue;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\druki_content\Data\ContentSyncRedirectQueueItem;
use Drupal\druki_content\Data\RedirectSourceFile;

/**
 * Provides redirect queue processor.
 */
final class ContentSyncRedirectQueueItemProcessor implements ContentSyncQueueProcessorInterface {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The state storage.
   */
  protected StateInterface $state;

  /**
   * The redirect storage.
   */
  protected ?EntityStorageInterface $redirectStorage = NULL;

  /**
   * ContentSyncRedirectQueueItemProcessor constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, StateInterface $state) {
    $this->entityTypeManager = $entity_type_manager;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public function process(ContentSyncQueueItemInterface $item): void {
    /** @var \Drupal\druki_content\Data\RedirectSourceFileList $files */
    $files = $item->getPayload();
    foreach ($files as $file) {
      $this->processRedirectFile($file);
    }
  }

  /**
   * Process single redirect file.
   *
   * @param \Drupal\druki_content\Data\RedirectSourceFile $redirect_file
   *   The redirect file.
   */
  protected function processRedirectFile(RedirectSourceFile $redirect_file): void {
    $state_id = 'druki_content:redirect_last_hash:' . $redirect_file->getLanguage();
    $previous_hash = $this->state->get($state_id);
    if ($previous_hash == $redirect_file->getHash()) {
      return;
    }
    $this->updateRedirects($redirect_file);
    $this->state->set($state_id, $redirect_file->getHash());
  }

  /**
   * Updated redirect entities.
   *
   * @param \Drupal\druki_content\Data\RedirectSourceFile $redirect_file
   *   The redirect file.
   */
  protected function updateRedirects(RedirectSourceFile $redirect_file): void {
    $this->cleanRedirects($redirect_file->getLanguage());
    $this->createRedirects($redirect_file);
  }

  /**
   * Cleans custom created redirects for provided langcode.
   *
   * @param string $language
   *   The langcode to clean.
   */
  protected function cleanRedirects(string $language): void {
    $query = $this->getRedirectStorage()->getQuery();
    $query->condition('druki_content_redirect', TRUE);
    $query->condition('language', $language);
    $redirect_ids = $query->execute();
    if (empty($redirect_ids)) {
      return;
    }
    $redirects = $this->getRedirectStorage()->loadMultiple($redirect_ids);
    $this->getRedirectStorage()->delete($redirects);
  }

  /**
   * Gets redirect storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The redirect storage.
   */
  protected function getRedirectStorage(): EntityStorageInterface {
    if (!isset($this->redirectStorage)) {
      $this->redirectStorage = $this->entityTypeManager->getStorage('redirect');
    }
    return $this->redirectStorage;
  }

  /**
   * Creates redirects from provided file.
   *
   * @param \Drupal\druki_content\Data\RedirectSourceFile $redirect_file
   *   The redirect file.
   */
  protected function createRedirects(RedirectSourceFile $redirect_file): void {
    if (($handle = \fopen($redirect_file->getPathname(), 'r')) !== FALSE) {
      while (($row = \fgetcsv($handle)) !== FALSE) {
        $from = UrlHelper::parse($row[0]);
        $to = UrlHelper::parse($row[1]);
        $redirect = $this->getRedirectStorage()->create();
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
   * {@inheritdoc}
   */
  public function isApplicable(ContentSyncQueueItemInterface $item): bool {
    return $item instanceof ContentSyncRedirectQueueItem;
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
    if (\strpos($raw_path, '?') !== FALSE) {
      $url = UrlHelper::parse($raw_path);
    }
    return $url;
  }

}
