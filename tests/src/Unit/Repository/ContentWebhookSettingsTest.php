<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\druki_content\Repository\ContentWebhookSettings;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for content webhook settings repository.
 *
 * @coversDefaultClass \Drupal\druki_content\Repository\ContentWebhookSettings
 */
final class ContentWebhookSettingsTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Builds a mock for key/value factory.
   *
   * @return \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   *   The mock of key/value factory.
   */
  protected function buildKeyValueFactory(): KeyValueFactoryInterface {
    $key_value_store = $this->prophesize(KeyValueStoreInterface::class);
    $key_value_store->get('content_update_access_key')->willReturn('default');
    $key_value_store->set('content_update_access_key', Argument::any())->will(function ($args) use ($key_value_store) {
      $key_value_store->get('content_update_access_key')->willReturn($args[1]);
    });

    $key_value_factory = $this->prophesize(KeyValueFactoryInterface::class);
    $key_value_factory->get('druki_content.webhook_settings')->willReturn($key_value_store->reveal());

    return $key_value_factory->reveal();
  }

  /**
   * Test that repository works as expected.
   */
  public function testRepository(): void {
    $settings = new ContentWebhookSettings($this->buildKeyValueFactory());

    $this->assertEquals('default', $settings->getContentUpdateWebhookAccessKey());
    $random_value = $this->getRandomGenerator()->string();
    $settings->setContentUpdateWebhookAccessKey($random_value);
    $this->assertEquals($random_value, $settings->getContentUpdateWebhookAccessKey());
  }

}
