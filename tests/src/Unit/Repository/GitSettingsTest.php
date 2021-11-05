<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Repository;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\druki_git\Repository\GitSettings;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for git settings repository.
 *
 * @coversDefaultClass \Drupal\druki_git\Repository\GitSettings
 */
final class GitSettingsTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that settings works as expected.
   */
  public function testSettings(): void {
    $settings = new GitSettings($this->getConfigFactory(), $this->getState());

    $this->assertEquals('default', $settings->getRepositoryUrl());
    $settings->setRepositoryUrl('https://example.com');
    $this->assertEquals('https://example.com', $settings->getRepositoryUrl());

    $this->assertEquals('default', $settings->getRepositoryPath());
    $settings->setRepositoryPath('public://test');
    $this->assertEquals('public://test', $settings->getRepositoryPath());

    $this->assertEquals('default', $settings->getWebhookAccessKey());
    $random_key = $this->getRandomGenerator()->string();
    $settings->setWebhookAccessKey($random_key);
    $this->assertEquals($random_key, $settings->getWebhookAccessKey());
  }

  /**
   * Tests that invalid repository path throws exception.
   */
  public function testInvalidRepositoryPath(): void {
    $settings = new GitSettings($this->getConfigFactory(), $this->getState());

    $this->expectException(\InvalidArgumentException::class);
    $settings->setRepositoryPath('https://example.com');
  }

  /**
   * Tests that invalid repository URL throws exception.
   */
  public function testInvalidRepositoryUrl(): void {
    $settings = new GitSettings($this->getConfigFactory(), $this->getState());

    $this->expectException(\InvalidArgumentException::class);
    $settings->setRepositoryUrl('random');
  }

  /**
   * Builds config factory mock.
   *
   * @return \Drupal\Core\Config\ConfigFactoryInterface
   *   The config factory instance.
   */
  protected function getConfigFactory(): ConfigFactoryInterface {
    $git_settings_config = $this->prophesize(Config::class);
    $git_settings_config->save()->willReturn(TRUE);

    $git_settings_config->get('repository_path')->willReturn('default');
    $git_settings_config->set('repository_path', Argument::any())->will(function ($args) use ($git_settings_config) {
      $git_settings_config->get('repository_path')->willReturn($args[1]);
      return $git_settings_config->reveal();
    });

    $git_settings_config->get('repository_url')->willReturn('default');
    $git_settings_config->set('repository_url', Argument::any())->will(function ($args) use ($git_settings_config) {
      $git_settings_config->get('repository_url')->willReturn($args[1]);
      return $git_settings_config->reveal();
    });

    $config_factory = $this->prophesize(ConfigFactoryInterface::class);
    $config_factory->getEditable('druki_git.git_settings')->willReturn($git_settings_config->reveal());
    return $config_factory->reveal();
  }

  /**
   * Builds state mock.
   *
   * @return \Drupal\Core\State\StateInterface
   *   The state instance.
   */
  protected function getState(): StateInterface {
    $state = $this->prophesize(StateInterface::class);
    $state->get('druki_git.webhook_key')->willReturn('default');
    $state->set('druki_git.webhook_key', Argument::any())->will(function ($args) use ($state) {
      $state->get('druki_git.webhook_key')->willReturn($args[1]);
    });
    return $state->reveal();
  }

}
