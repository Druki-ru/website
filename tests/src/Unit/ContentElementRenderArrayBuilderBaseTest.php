<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\Core\Cache\Cache;
use Drupal\druki_content\Builder\ContentElementRenderArrayBuilderBase;
use Drupal\druki_content\Data\ContentElementBase;
use Drupal\druki_content\Data\ContentElementInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for content element render array builder base class.
 *
 * @coversDefaultClass \Drupal\druki_content\Builder\ContentElementRenderArrayBuilderBase
 */
final class ContentElementRenderArrayBuilderBaseTest extends UnitTestCase {

  /**
   * Tests that class works as expected.
   */
  public function testObject(): void {
    $element = new class() extends ContentElementBase {
      public function getContent(): string {
        return 'Hello World!';
      }
    };

    $builder = new class() extends ContentElementRenderArrayBuilderBase {
      public static function isApplicable(ContentElementInterface $element): bool {
        return TRUE;
      }

      public function build(ContentElementInterface $element, array $children_render_array = []): array {
        return ['#markup' => $element->getContent()];
      }
    };

    $this->assertSame([], $builder->getCacheContexts());
    $this->assertSame([], $builder->getCacheTags());
    $this->assertSame(Cache::PERMANENT, $builder->getCacheMaxAge());
    $this->assertSame(['#markup' => $element->getContent()], $builder->build($element));
  }

}
