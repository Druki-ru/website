<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\druki_content\Repository\ContentSettings;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for git settings repository.
 *
 * @coversDefaultClass \Drupal\druki_content\Repository\ContentSettings
 */
final class ContentSettingsTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that settings works as expected.
   */
  public function testSettings(): void {
    $settings = new ContentSettings($this->getKeyValueFactory());

    $this->assertEquals('default', $settings->getRepositoryUrl());
    $settings->setRepositoryUrl('https://example.com');
    $this->assertEquals('https://example.com', $settings->getRepositoryUrl());

    $this->assertEquals('default', $settings->getContentSourceUri());
    $settings->setContentSourceUri('public://test');
    $this->assertEquals('public://test', $settings->getContentSourceUri());

    $this->assertEquals('default', $settings->getContentUpdateWebhookAccessKey());
    $random_value = $this->getRandomGenerator()->string();
    $settings->setContentUpdateWebhookAccessKey($random_value);
    $this->assertEquals($random_value, $settings->getContentUpdateWebhookAccessKey());
  }

  /**
   * Builds key/value factory mock.
   *
   * @return \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   *   The config factory instance.
   */
  protected function getKeyValueFactory(): KeyValueFactoryInterface {
    $content_settings = $this->prophesize(KeyValueStoreInterface::class);

    $content_settings->get('content_source_uri')->willReturn('default');
    $content_settings->set('content_source_uri', Argument::any())->will(static function ($args) use ($content_settings) {
      $content_settings->get('content_source_uri')->willReturn($args[1]);
      return $content_settings->reveal();
    });

    $content_settings->get('repository_url')->willReturn('default');
    $content_settings->set('repository_url', Argument::any())->will(static function ($args) use ($content_settings) {
      $content_settings->get('repository_url')->willReturn($args[1]);
      return $content_settings->reveal();
    });

    $content_settings->get('webhook_url')->willReturn('default');
    $content_settings->set('webhook_url', Argument::any())->will(static function ($args) use ($content_settings) {
      $content_settings->get('webhook_url')->willReturn($args[1]);
      return $content_settings->reveal();
    });

    $key_value_factory = $this->prophesize(KeyValueFactoryInterface::class);
    $key_value_factory->get('druki_content_settings')->willReturn($content_settings->reveal());
    return $key_value_factory->reveal();
  }

}
