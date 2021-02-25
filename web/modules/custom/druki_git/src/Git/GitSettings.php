<?php

namespace Drupal\druki_git\Git;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides implementation for git settings.
 */
final class GitSettings implements GitSettingsInterface {

  /**
   * The config name.
   */
  protected const CONFIG_NAME = 'druki_git.git_settings';

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * GitSettings constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryPath(): ?string {
    return $this->configFactory->get(self::CONFIG_NAME)->get('repository_path');
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryUrl(): ?string {
    return $this->configFactory->get(self::CONFIG_NAME)->get('repository_url');
  }

}
