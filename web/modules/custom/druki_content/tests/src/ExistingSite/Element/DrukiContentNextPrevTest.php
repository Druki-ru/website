<?php

namespace Drupal\Tests\druki_content\ExistingSite\Element;

use Drupal\Core\Link;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for druki content next and previous links.
 *
 * @covers \Drupal\druki_content\Element\ContentNextPrev
 */
final class DrukiContentNextPrevTest extends ExistingSiteBase {

  /**
   * The renderer.
   */
  protected Renderer $renderer;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->renderer = $this->container->get('renderer');
  }

  /**
   * Test behavior when no links provided.
   */
  public function testNoLinks(): void {
    $element = [
      '#type' => 'druki_content_next_prev',
    ];
    $html = $this->renderer->renderPlain($element);
    $this->assertEmpty($html);
  }

  /**
   * Test behavior when both links are presented.
   */
  public function testBothLink(): void {
    $url = Url::fromRoute('<front>')->setAbsolute();
    $prev = Link::fromTextAndUrl('Previous', $url);
    $next = Link::fromTextAndUrl('Next', $url);

    $element = [
      '#type' => 'druki_content_next_prev',
      '#prev_link' => $prev,
      '#next_link' => $next,
    ];

    $html = $this->renderer->renderPlain($element);
    $this->assertStringContainsString('Previous', $html);
    $this->assertStringContainsString('Next', $html);
    $this->assertStringContainsString($url->toString(), $html);
  }

}
