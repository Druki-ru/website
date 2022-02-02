<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Kernel\Builder;

use Drupal\druki_content\Builder\ContentAsideElementRenderArrayBuilder;
use Drupal\druki_content\Data\ContentAsideElement;
use Drupal\druki_content\Data\ContentElementInterface;
use Drupal\druki_content_test\Data\ContentNotExpectedElement;
use Drupal\Tests\druki_content\Kernel\DrukiContentKernelTestBase;

/**
 * Provides test for converting aside element into render array.
 *
 * @coversDefaultClass \Drupal\druki_content\Builder\ContentAsideElementRenderArrayBuilder
 */
final class ContentAsideElementRenderArrayBuilderTest extends DrukiContentKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'druki_content_test',
  ];

  /**
   * The aside element render array builder.
   */
  protected ?ContentAsideElementRenderArrayBuilder $builder;

  /**
   * Tests render array builder.
   *
   * @param \Drupal\druki_content\Data\ContentElementInterface $element
   *   An element for testing.
   * @param bool $is_applicable
   *   An expected 'isApplicable' call result.
   * @param array $expected_render_array
   *   An array with expected result.
   *
   * @dataProvider elementProvider
   */
  public function testBuilder(ContentElementInterface $element, bool $is_applicable, array $expected_render_array): void {
    $actual_is_applicable = $this->builder::isApplicable($element);
    $this->assertEquals($is_applicable, $actual_is_applicable);
    // If element is not applicable we cant process next assertions.
    if (!$actual_is_applicable) {
      return;
    }
    $this->assertEquals($expected_render_array, $this->builder->build($element));
  }

  /**
   * Provides data for testing.
   *
   * @return array
   *   An array with data.
   */
  public function elementProvider(): array {
    $data = [];

    $data['not applicable'] = [
      new ContentNotExpectedElement(),
      FALSE,
      [],
    ];

    $data['just type'] = [
      new ContentAsideElement('warning'),
      TRUE,
      [
        '#theme' => 'druki_content_element_aside',
        '#aside_type' => 'warning',
        '#aside_header' => NULL,
        '#content' => [],
      ],
    ];

    $data['with header'] = [
      new ContentAsideElement('important', 'Hello, world!'),
      TRUE,
      [
        '#theme' => 'druki_content_element_aside',
        '#aside_type' => 'important',
        '#aside_header' => 'Hello, world!',
        '#content' => [],
      ],
    ];

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->builder = $this->container->get('druki_content.builder.content_aside_element_render_array');
  }

}
