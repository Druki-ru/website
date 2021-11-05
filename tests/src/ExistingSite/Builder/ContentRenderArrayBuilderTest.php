<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Builder;

use Drupal\Core\Cache\Cache;
use Drupal\druki_content\Builder\ContentElementRenderArrayBuilderBase;
use Drupal\druki_content\Builder\ContentElementRenderArrayBuilderInterface;
use Drupal\druki_content\Builder\ContentRenderArrayBuilder;
use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentElementBase;
use Drupal\druki_content\Data\ContentElementInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content render array builder.
 *
 * @coversDefaultClass \Drupal\druki_content\Builder\ContentRenderArrayBuilder
 */
final class ContentRenderArrayBuilderTest extends ExistingSiteBase {

  /**
   * Creates an instance with fake content element.
   *
   * @param string $name
   *   The element name.
   * @param string $content
   *   The lement content.
   *
   * @return \Drupal\druki_content\Data\ContentElementInterface
   *   The instance for fake element.
   */
  protected function createStubContentElement(string $name, string $content): ContentElementInterface {
    return new class($name, $content) extends ContentElementBase {

      protected $name;
      protected $content;

      public function __construct(string $name, string $content) {
        $this->name = $name;
        $this->content = $content;
      }

      public function getName(): string {
        return $this->name;
      }

      public function getContent(): string {
        return $this->content;
      }
    };
  }

  /**
   * Tests that build works as expected.
   *
   * @covers ::build()
   */
  public function testBuild(): void {
    $builder_first = new class() extends ContentElementRenderArrayBuilderBase {

      /**
       * {@inheritdoc}
       */
      public static function isApplicable(ContentElementInterface $element): bool {
        return $element->getName() == 'first';
      }

      /**
       * {@inheritdoc}
       */
      public function build(ContentElementInterface $element, array $children_render_array = []): array {
        return [
          '#markup' => $element->getContent(),
        ];
      }

    };

    $builder_second = new class() extends ContentElementRenderArrayBuilderBase {

      /**
       * {@inheritdoc}
       */
      public static function isApplicable(ContentElementInterface $element): bool {
        return $element->getName() == 'second';
      }

      /**
       * {@inheritdoc}
       */
      public function build(ContentElementInterface $element, array $children_render_array = []): array {
        return [
          '#markup' => $element->getContent(),
        ];
      }

    };

    $builder = new ContentRenderArrayBuilder();
    // We expect that build for first element wil be selected properly.
    $builder->addBuilder($builder_second);
    $builder->addBuilder($builder_first);

    $element_first = $this->createStubContentElement('first', 'Hello First!');
    $element_second = $this->createStubContentElement('second', 'Hello Second!');
    $content = new Content();
    $content->addElement($element_first);
    $content->addElement($element_second);
    $build = $builder->build($content);
    $expected = [
      [
        '#markup' => 'Hello First!',
        '#cache' => [
          'contexts' => [],
          'tags' => [],
          'max-age' => Cache::PERMANENT,
        ],
      ],
      [
        '#markup' => 'Hello Second!',
        '#cache' => [
          'contexts' => [],
          'tags' => [],
          'max-age' => Cache::PERMANENT,
        ],
      ],
    ];
    $this->assertEquals($expected, $build);
  }

}
