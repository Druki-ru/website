<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\redirect\Entity\Redirect;

/**
 * Provides processor for redirect file list queue item.
 */
final class RedirectFileListQueueItemProcessor implements RedirectSyncQueueItemProcessorInterface {

  /**
   * The redirect storage.
   */
  protected EntityStorageInterface $redirectStorage;

  /**
   * Constructs a new RedirectFileListQueueItemProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->redirectStorage = $entity_type_manager->getStorage('redirect');
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(RedirectSyncQueueItemInterface $item): bool {
    return $item instanceof RedirectFileListQueueItem;
  }

  /**
   * {@inheritdoc}
   */
  public function process(RedirectSyncQueueItemInterface $item): array {
    \assert($item instanceof RedirectFileListQueueItem);
    $ids = [];
    /** @var \Drupal\druki_redirect\Data\RedirectFile $redirect_file */
    foreach ($item->getPayload()->getIterator() as $redirect_file) {
      $processed_ids = $this->processRedirectFile($redirect_file);
      $ids = \array_unique(\array_merge($processed_ids, $ids));
    }
    return $ids;
  }

  /**
   * Process single redirect file.
   *
   * @param \Drupal\druki_redirect\Data\RedirectFile $redirect_file
   *   The redirect file.
   */
  protected function processRedirectFile(RedirectFile $redirect_file): array {
    $handle = \fopen($redirect_file->getPathname(), 'r');
    if (!$handle) {
      return [];
    }
    $ids = [];
    while (($row = \fgetcsv($handle)) !== FALSE) {
      $from = UrlHelper::parse($row[0]);
      $to = UrlHelper::parse($row[1]);
      $redirect = $this->prepareRedirectEntity($from, $to, $redirect_file->getLanguage());

      if ($redirect->isNew()) {
        $redirect->setLanguage($redirect_file->getLanguage());
        $redirect->setStatusCode(301);
        $redirect->setSource($from['path'], $from['query']);
        $redirect->setRedirect($to['path'], $to['query'], ['fragment' => $to['fragment']]);
        $redirect->set('druki_content_redirect', TRUE);
        $redirect->save();
      }

      $ids[] = $redirect->id();
    }
    return $ids;
  }

  /**
   * Loads or creates redirect entity.
   *
   * @param array $from
   *   An array with from value.
   * @param array $to
   *   An array with to value.
   * @param string $langcode
   *   The language of redirect.
   *
   * @return \Drupal\redirect\Entity\Redirect
   *   The redirect entity.
   *
   * @todo Extract that search into RedirectRepository.
   */
  protected function prepareRedirectEntity(array $from, array $to, string $langcode): Redirect {
    $hash = Redirect::generateHash($from['path'], $from['query'], $langcode);
    $redirect_uri = $this->prepareRedirectUri($to);

    $redirect_ids = $this->redirectStorage->getQuery()->accessCheck(FALSE)
      ->condition('hash', $hash)
      ->condition('redirect_redirect.uri', $redirect_uri['uri'])
      ->condition('redirect_redirect.options', \serialize($redirect_uri['options']))
      ->condition('language', $langcode)
      ->execute();
    if (!$redirect_ids) {
      return $this->redirectStorage->create();
    }
    return $this->redirectStorage->load(\array_shift($redirect_ids));
  }

  /**
   * Prepare redirect URI value.
   *
   * @param array $to
   *   An array with redirect params.
   *
   * @return array
   *   An array for 'link' field values.
   */
  protected function prepareRedirectUri(array $to): array {
    $uri = $to['path'] . ($to['query'] ? '?' . UrlHelper::buildQuery($to['query']) : '');
    $external = UrlHelper::isValid($to['path'], TRUE);
    $uri = ($external ? $to['path'] : 'internal:/' . \ltrim($uri, '/'));
    return [
      'uri' => $uri,
      'options' => [
        'fragment' => $to['fragment'],
      ],
    ];
  }

}
