<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

use Drupal\druki_redirect\Data\Redirect;
use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\druki_redirect\Repository\RedirectRepositoryInterface;

/**
 * Provides processor for redirect file list queue item.
 */
final class RedirectFileListQueueItemProcessor implements RedirectSyncQueueItemProcessorInterface {

  /**
   * The redirect repository.
   */
  protected RedirectRepositoryInterface $redirectRepository;

  /**
   * Constructs a new RedirectFileListQueueItemProcessor object.
   *
   * @param \Drupal\druki_redirect\Repository\RedirectRepositoryInterface $redirect_repository
   *   The redirect repository.
   */
  public function __construct(RedirectRepositoryInterface $redirect_repository) {
    $this->redirectRepository = $redirect_repository;
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
      $ids[] = $this->processRedirectRow($row, $redirect_file->getLanguage());
    }
    return $ids;
  }

  /**
   * Process single redirect row from redirect file.
   *
   * @param array $row
   *   The row values contains:
   *   - 0: The source of redirect (from).
   *   - 1: The redirect destination (to).
   * @param string $language
   *   The redirect language.
   *
   * @return int
   *   The updated or created redirect entity.
   */
  protected function processRedirectRow(array $row, string $language): int {
    $redirect = Redirect::createFromUserInput($row[0], $row[1]);
    if ($redirect_entity = $this->redirectRepository->loadRedirect($redirect, $language)) {
      $redirect_entity_data = Redirect::createFromRedirectEntity($redirect_entity);
      // If this condition passes, that means the redirect URL is changed but
      // the source URL remains the same and we should update entity.
      if ($redirect->checksum() != $redirect_entity_data->checksum()) {
        $redirect_entity->setRedirect(
          $redirect->getRedirect()->getPath(),
          $redirect->getRedirect()->getQuery(),
          ['fragment' => $redirect->getRedirect()->getFragment()],
        );
        $redirect_entity->save();
      }
    }
    else {
      $redirect_entity = $this->redirectRepository->createRedirect($redirect, $language);
    }
    return (int) $redirect_entity->id();
  }

}
