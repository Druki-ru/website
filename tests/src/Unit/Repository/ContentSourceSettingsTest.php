<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Repository;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\druki_content\Repository\ContentSourceSettings;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for git settings repository.
 *
 * @coversDefaultClass \Drupal\druki_content\Repository\ContentSourceSettings
 */
final class ContentSourceSettingsTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that settings works as expected.
   */
  public function testSettings(): void {
    $settings = new ContentSourceSettings($this->getConfigFactory());

    $this->assertEquals('default', $settings->getRepositoryUrl());
    $settings->setRepositoryUrl('https://example.com');
    $this->assertEquals('https://example.com', $settings->getRepositoryUrl());

    $this->assertEquals('default', $settings->getRepositoryUri());
    $settings->setRepositoryUri('public://test');
    $this->assertEquals('public://test', $settings->getRepositoryUri());
  }

  /**
   * Builds config factory mock.
   *
   * @return \Drupal\Core\Config\ConfigFactoryInterface
   *   The config factory instance.
   */
  protected function getConfigFactory(): ConfigFactoryInterface {
    $content_source_settings = $this->prophesize(Config::class);
    $content_source_settings->save()->willReturn(TRUE);

    $content_source_settings->get('repository_uri')->willReturn('default');
    $content_source_settings->set('repository_uri', Argument::any())->will(function ($args) use ($content_source_settings) {
      $content_source_settings->get('repository_uri')->willReturn($args[1]);
      return $content_source_settings->reveal();
    });

    $content_source_settings->get('repository_url')->willReturn('default');
    $content_source_settings->set('repository_url', Argument::any())->will(function ($args) use ($content_source_settings) {
      $content_source_settings->get('repository_url')->willReturn($args[1]);
      return $content_source_settings->reveal();
    });

    $config_factory = $this->prophesize(ConfigFactoryInterface::class);
    $config_factory->getEditable('druki_content.content_source_settings')->willReturn($content_source_settings->reveal());
    return $config_factory->reveal();
  }

  /**
   * Tests that invalid repository path throws exception.
   */
  public function testInvalidRepositoryPath(): void {
    $settings = new ContentSourceSettings($this->getConfigFactory());

    $this->expectException(\InvalidArgumentException::class);
    $settings->setRepositoryUri('https://example.com');
  }

  /**
   * Tests that invalid repository URL throws exception.
   */
  public function testInvalidRepositoryUrl(): void {
    $settings = new ContentSourceSettings($this->getConfigFactory());

    $this->expectException(\InvalidArgumentException::class);
    $settings->setRepositoryUrl('random');
  }

}
