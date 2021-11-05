<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Builder;

use Drupal\druki_content\Builder\ContentElementRenderArrayBuilderInterface;
use Drupal\druki_content\Data\ContentCodeElement;
use Drupal\druki_content\Data\ContentElementBase;
use Drupal\druki_content\Data\ContentTextElement;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content text element render array builder.
 *
 * @coversDefaultClass \Drupal\druki_content\Builder\ContentCodeElementRenderArrayBuilder
 */
final class ContentCodeElementRenderArrayBuilderTest extends ExistingSiteBase {

  /**
   * The builder.
   */
  protected ContentElementRenderArrayBuilderInterface $builder;

  /**
   * Tests that applicable test works as expected.
   *
   * @covers ::isApplicable()
   */
  public function testIsApplicable(): void {
    $broken_element = new class() extends ContentElementBase {};
    $this->assertFalse($this->builder->isApplicable($broken_element));

    $valid_element = new ContentCodeElement("echo 'Hello World!'");
    $this->assertTrue($this->builder->isApplicable($valid_element));
  }

  /**
   * Tests that build works as expected.
   *
   * @covers ::build()
   */
  public function testBuild(): void {
    $element = new ContentCodeElement("echo 'Hello World!'", 'php');
    $expected = [
      '#theme' => 'druki_content_element_code',
      '#content' => [
        '#type' => 'processed_text',
        '#text' => $element->getContent(),
        '#format' => 'basic_html',
      ],
      '#language' => 'php',
    ];
    $this->assertEquals($expected, $this->builder->build($element));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->builder = $this->container->get('druki_content.builder.content_code_element_render_array');
  }

}
