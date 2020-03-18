<?php

namespace Drupal\Tests\druki\Kernel\Element;

use Drupal\KernelTests\AssertContentTrait;
use Drupal\KernelTests\KernelTestBase;

/**
 * Provides test for card element.
 *
 * @coversDefaultClass \Drupal\druki\Element\Card
 */
final class CardTest extends KernelTestBase {

  use AssertContentTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['druki', 'system', 'update'];

  /**
   * Tests card render element markup.
   *
   * @dataProvider cardElementProvider
   */
  public function testCardElementMarkup(array $element, array $expected_raws, array $expected_no_raws) {
    $this->render($element);

    foreach ($expected_raws as $expected_raw) {
      $this->assertRaw($expected_raw);
    }

    foreach ($expected_no_raws as $expected_no_raw) {
      $this->assertNoRaw($expected_no_raw);
    }
  }

  /**
   * Provides testing data.
   */
  public function cardElementProvider() {
    return [
      'element without any params' => [
        [
          '#type' => 'druki_card',
        ],
        [
          '<div class="druki-card druki-card--elevated">',
          '<div class="druki-card__title"></div>',
          '<div class="druki-card__primary-action">',
        ],
        [],
      ],
      'title' => [
        [
          '#type' => 'druki_card',
          '#title' => 'Just title',
        ],
        [
          '<div class="druki-card__title">Just title</div>',
        ],
        [],
      ],
      'subhead' => [
        [
          '#type' => 'druki_card',
          '#subhead' => 'Subhead',
        ],
        [
          '<div class="druki-card__subtitle">Subhead</div>',
        ],
        [],
      ],
      'supporting text' => [
        [
          '#type' => 'druki_card',
          '#supporting_text' => 'The supporting text.',
        ],
        [
          '<div class="druki-card__secondary">The supporting text.</div>',
        ],
        [],
      ],
      'style' => [
        [
          '#type' => 'druki_card',
          '#style' => 'outline',
        ],
        [
          '<div class="druki-card druki-card--outline">',
        ],
        [
          '<div class="druki-card druki-card--elevated">',
        ],
      ],
      'primary url' => [
        [
          '#type' => 'druki_card',
          '#primary_url' => 'https://google.com/',
        ],
        [
          '<a href="https://google.com/" class="druki-card__primary-action druki-card__primary-action--link">',
        ],
        [
          '<div class="druki-card__primary-action">',
        ],
      ],
    ];
  }

}
