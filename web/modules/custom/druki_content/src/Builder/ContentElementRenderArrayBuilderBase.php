<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\Core\Cache\Cache;

/**
 * Provides an abstract class for every content element render array builder.
 */
abstract class ContentElementRenderArrayBuilderBase implements ContentElementRenderArrayBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return Cache::PERMANENT;
  }

}
