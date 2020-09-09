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
  public function testCardElementMarkup(array $element, array $expected_raws) {
    $this->render($element);

    foreach ($expected_raws as $expected_raw) {
      $this->assertRaw($expected_raw);
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
          '<div class="druki-card">',
          '<div class="druki-card__content">',
        ],
      ],
      'title' => [
        [
          '#type' => 'druki_card',
          '#title' => 'Just title',
        ],
        [
          '<div class="druki-card__title">Just title</div>',
        ],
      ],
      'subhead' => [
        [
          '#type' => 'druki_card',
          '#subhead' => 'Subhead',
        ],
        [
          '<div class="druki-card druki-card--with-subhead">',
          '<div class="druki-card__subhead">Subhead</div>',
        ],
      ],
      'description' => [
        [
          '#type' => 'druki_card',
          '#description' => 'The description.',
        ],
        [
          '<div class="druki-card__description">The description.</div>',
        ],
      ],
      'primary url' => [
        [
          '#type' => 'druki_card',
          '#primary_url' => 'https://google.com/',
        ],
        [
          '<a href="https://google.com/" class="button button--primary button--small">',
        ],
      ],
      // @todo Fix this:
      // PHP Fatal error: Uncaught Drupal\Core\DependencyInjection\ContainerNotInitializedException: \Drupal::$container is not initialized yet. \Drupal::setContainer() must be called with a real container.
//      'actions' => [
//        [
//          '#type' => 'druki_card',
//          '#actions' => [
//            '#type' => 'Link',
//            '#title' => 'Google',
//            '#url' => Url::fromUri('https://google.com'),
//          ],
//        ],
//        [
//          'test'
//        ],
//      ],
    ];
  }

}
