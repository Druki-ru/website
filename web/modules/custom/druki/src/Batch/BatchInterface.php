<?php

declare(strict_types=1);

namespace Drupal\druki\Batch;

use Drupal\Core\Batch\BatchBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Defines an interface for batch processors with pseudo DI implementation.
 */
interface BatchInterface {

  /**
   * Builds a batch object.
   *
   * @param array $args
   *   Arguments used to build batch.
   *
   * @return \Drupal\Core\Batch\BatchBuilder
   *   A batch object.
   */
  public static function build(array ...$args): BatchBuilder;

  /**
   * Prepare and process a single batch operation.
   *
   * @param string $method
   *   A method to call for operation in this instance.
   * @param array $build_args
   *   An array with build arguments.
   * @param array $context
   *   A batch context.
   */
  public static function processOperation(string $method, array $build_args = [], array &$context = []): void;

  /**
   * Gets a callable for batch operation.
   *
   * @return callable
   *   A callable for main operations.
   */
  public static function getProcessCallable(): callable;

  /**
   * Process a batch finish callback.
   *
   * @param bool $success
   *   A batch status.
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
  public static function processFinish(bool $success, array $results, array $operations, string $duration): ?RedirectResponse;

  /**
   * Gets a callable for batch operation.
   *
   * @param string $finish_method
   *   A method name for finish operation.
   *
   * @return callable
   *   A callable for finish operation.
   */
  public static function getProcessFinishCallable(string $finish_method): callable;

  /**
   * Creates an instance of current batch processor.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   *
   * @return $this
   */
  public static function createInstance(ContainerInterface $container): static;

  /**
   * Sets a build args.
   *
   * @param array $args
   *   A build args.
   *
   * @return $this
   */
  public function setBuildArgs(array ...$args): static;

  /**
   * Gets a build args.
   *
   * @return array
   *   An array with build args.
   */
  public function getBuildArgs(): array;

}
