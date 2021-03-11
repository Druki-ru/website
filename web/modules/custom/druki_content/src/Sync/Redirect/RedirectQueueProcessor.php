<?php

namespace Drupal\druki_content\Sync\Redirect;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\druki_content\Sync\Queue\QueueItemInterface;
use Drupal\druki_content\Sync\Queue\QueueProcessorInterface;

/**
 * Provides redirect queue processor.
 */
final class RedirectQueueProcessor implements QueueProcessorInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The redirect storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|null
   */
  protected $redirectStorage;

  /**
   * RedirectQueueProcessor constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, StateInterface $state) {
    $this->entityTypeManager = $entity_type_manager;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public function process(QueueItemInterface $item): void {
    /** @var \Drupal\druki_content\Sync\Redirect\RedirectFileList $files */
    $files = $item->getPayload();
    foreach ($files as $file) {
      $this->processRedirectFile($file);
    }
  }

  /**
   * Process single redirect file.
   *
   * @param \Drupal\druki_content\Sync\Redirect\RedirectFile $redirect_file
   *   The redirect file.
   */
  protected function processRedirectFile(RedirectFile $redirect_file): void {
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
   * @param \Drupal\druki_content\Sync\Redirect\RedirectFile $redirect_file
   *   The redirect file.
   */
  protected function updateRedirects(RedirectFile $redirect_file): void {
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
   * @param \Drupal\druki_content\Sync\Redirect\RedirectFile $redirect_file
   *   The redirect file.
   */
  protected function createRedirects(RedirectFile $redirect_file): void {
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
    if (strpos($raw_path, '?') !== FALSE) {
      $url = UrlHelper::parse($raw_path);
    }
    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(QueueItemInterface $item): bool {
    return $item instanceof RedirectQueueItem;
  }

}
