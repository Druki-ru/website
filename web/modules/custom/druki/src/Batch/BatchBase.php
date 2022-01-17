<?php

declare(strict_types=1);

namespace Drupal\druki\Batch;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * A base implementation for batch processors using OOP and sort of DI.
 */
abstract class BatchBase implements BatchInterface {

  /**
   * An array with build args.
   */
  protected array $buildArgs = [];

  /**
   * {@inheritdoc}
   */
  public static function processOperation(string $method, array $build_args = [], array &$context = []): void {
    $container = \Drupal::getContainer();
    $instance = static::createInstance($container);
    if (!\method_exists($instance, $method)) {
      return;
    }
    $instance->setBuildArgs($build_args);
    \call_user_func_array([$instance, $method], [&$context]);
  }

  /**
   * {@inheritdoc}
   */
  public static function getProcessCallable(): callable {
    return [static::class, 'processOperation'];
  }

  /**
   * {@inheritdoc}
   */
  public static function processFinish(bool $success, array $results, array $operations, string $duration): ?RedirectResponse {
    $container = \Drupal::getContainer();
    $instance = static::createInstance($container);
    if (!\method_exists($instance, 'finishCallback')) {
      return NULL;
    }
    return \call_user_func_array(
      [$instance, 'finishCallback'],
      [$success, $results, $operations, $duration],
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function getProcessFinishCallable(string $finish_method): callable {
    return [static::class, 'processFinish'];
  }

  /**
   * {@inheritdoc}
   */
  public function getBuildArgs(): array {
    return $this->buildArgs;
  }

  /**
   * {@inheritdoc}
   */
  public function setBuildArgs(...$args): static {
    $this->buildArgs = $args;
    return $this;
  }

}
