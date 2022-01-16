<?php

declare(strict_types=1);

namespace Drupal\druki_content\Batch;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\druki_content\Repository\ContentStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides content invalidation batch.
 */
final class ContentInvalidateAllBatch {

  /**
   * The content storage.
   */
  protected ContentStorage $contentStorage;

  /**
   * The messenger.
   */
  protected MessengerInterface $messenger;

  /**
   * Builds a batch.
   *
   * @return \Drupal\Core\Batch\BatchBuilder
   *   The batch builder.
   */
  public static function build(): BatchBuilder {
    $callable_operation = [self::class, 'processOperation'];

    $builder = new BatchBuilder();
    $builder->setTitle(new TranslatableMarkup('Invalidate content'));
    $builder->addOperation($callable_operation, ['findContentIds']);
    $builder->addOperation($callable_operation, ['invalidateContent']);
    $builder->setFinishCallback([self::class, 'processFinishCallback']);
    return $builder;
  }

  /**
   * Prepare and process a single batch operation.
   *
   * @param string $method
   *   The method to call for processing.
   * @param array $context
   *   An array with batch context.
   */
  public static function processOperation(string $method, array &$context = []): void {
    $instance = self::createInstance();
    if (!\method_exists($instance, $method)) {
      return;
    }
    \call_user_func_array([$instance, $method], [&$context]);
  }

  /**
   * Creates an instance of current batch processor.
   *
   * @return $this
   */
  public static function createInstance(): self {
    $container = \Drupal::getContainer();
    $instance = new self();
    $instance->contentStorage = $container->get('entity_type.manager')->getStorage('druki_content');
    $instance->messenger = $container->get('messenger');
    return $instance;
  }

  /**
   * Process batch finish callback.
   *
   * @param bool $success
   *   Indicates batch processing result.
   * @param array $results
   *   An array with results.
   * @param array $operations
   *   An array with batch operations.
   * @param string $duration
   *   A batch duration formatted result.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
   *   The redirect response for batch, NULL otherwise.
   */
  public static function processFinishCallback(bool $success, array $results, array $operations, string $duration): ?RedirectResponse {
    $instance = self::createInstance();
    if (!\method_exists($instance, 'finishCallback')) {
      return NULL;
    }
    return \call_user_func_array(
      [$instance, 'finishCallback'],
      [$success, $results, $operations, $duration],
    );
  }

  /**
   * The batch operation to find all content IDs.
   *
   * @param array $context
   *   A batch context.
   */
  protected function findContentIds(array &$context): void {
    $context['results']['entity_ids'] = $this->contentStorage->getQuery()
      ->accessCheck(FALSE)
      ->execute();
    $context['message'] = new TranslatableMarkup("Finding content ID's");
  }

  /**
   * The batch operation to invalidate content.
   *
   * @param array $context
   *   A batch context.
   */
  protected function invalidateContent(array &$context): void {
    if (empty($context['results']['entity_ids'])) {
      $context['finished'] = 1;
      return;
    }

    $sandbox = &$context['sandbox'];
    if (!isset($sandbox['entity_id_chunks'])) {
      $entity_ids = $context['results']['entity_ids'];
      $chunk_size = Settings::get('entity_update_batch_size', 50);
      $sandbox['entity_id_chunks'] = \array_chunk($entity_ids, $chunk_size);
      $sandbox['total'] = \count($sandbox['entity_id_chunks']);
      $sandbox['current'] = 0;
    }

    $ids_chunk = $sandbox['entity_id_chunks'][$sandbox['current']];
    $entities = $this->contentStorage->loadMultiple($ids_chunk);
    /** @var \Drupal\druki_content\Entity\ContentInterface $entity */
    foreach ($entities as $entity) {
      $entity->setSourceHash('invalidated');
      $entity->save();
    }
    $sandbox['current']++;

    $context['finished'] = $sandbox['current'] / $sandbox['total'];
    $context['message'] = new TranslatableMarkup('Invalidating content chunks: @current of @total', [
      '@current' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

  /**
   * The batch operation to invalidate content.
   *
   * @param bool $success
   *   Indicates batch processing result.
   * @param array $results
   *   An array with results.
   * @param array $operations
   *   An array with batch operations.
   * @param string $duration
   *   A batch duration formatted result.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
   *   The redirect response for batch, NULL otherwise.
   */
  protected function finishCallback(bool $success, array $results, array $operations, string $duration): ?RedirectResponse {
    $message = new TranslatableMarkup('All content has been invalidated in @duration. It will be updated on next synchronization.', [
      '@duration' => $duration,
    ]);
    $this->messenger->addStatus($message);
    $redirect_url = Url::fromRoute('entity.druki_content.collection');
    return new RedirectResponse($redirect_url->toString());
  }

}
