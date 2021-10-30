<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentElementInterface;

/**
 * Provides class to build render array for content.
 *
 * This class builds render array from structured Content to Drupal's render
 * array to be used for further needs. For example, convert it to HTML using
 * custom templates and markup with proper cache metadata.
 */
final class ContentRenderArrayBuilder {

  /**
   * An array with content element render array builders.
   *
   * @var \Drupal\druki_content\Builder\ContentElementRenderArrayBuilderInterface[]
   */
  protected array $builders;

  /**
   * Adds render array builder for content element.
   *
   * @param \Drupal\druki_content\Builder\ContentElementRenderArrayBuilderInterface $builder
   *   The builder instance.
   */
  public function addBuilder(ContentElementRenderArrayBuilderInterface $builder): void {
    $this->builders[] = $builder;
  }

  /**
   * Builds render array from content.
   *
   * @param \Drupal\druki_content\Data\Content $content
   *   The structured content.
   *
   * @return array
   *   The render array with content.
   */
  public function build(Content $content): array {
    $build = [];
    /** @var \Drupal\druki_content\Data\ContentElementInterface $element */
    foreach ($content->getElements() as $element) {
      $build[] = $this->buildElement($element);
    }
    return $build;
  }

  /**
   * Builds a single content element.
   *
   * @param \Drupal\druki_content\Data\ContentElementInterface $element
   *   The content element.
   *
   * @return array
   *   The result render array.
   */
  protected function buildElement(ContentElementInterface $element): array {
    $children_build = [];
    if ($element->hasChildren()) {
      foreach ($element->getChildren() as $child) {
        $children_build[] = $this->buildElement($child);
      }
    }
    $element_build = [];
    foreach ($this->builders as $builder) {
      if (!$builder::isApplicable($element)) {
        continue;
      }
      $element_build = $builder->build($element, $children_build);
      // Add every dynamically created cache metadata into build.
      $element_cache_metadata = new CacheableMetadata();
      $element_cache_metadata->setCacheContexts($builder->getCacheContexts());
      $element_cache_metadata->setCacheTags($builder->getCacheTags());
      $element_cache_metadata->setCacheMaxAge($builder->getCacheMaxAge());
      $element_cache_metadata->applyTo($element_build);
      break;
    }
    return $element_build;
  }

}
