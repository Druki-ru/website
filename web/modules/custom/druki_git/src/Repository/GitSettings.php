<?php

declare(strict_types=1);

namespace Drupal\druki_git\Repository;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;

/**
 * Provides a repository to manage git settings.
 */
final class GitSettings implements GitSettingsInterface {

  /**
   * The config name.
   */
  protected const CONFIG_NAME = 'druki_git.git_settings';

  /**
   * The storage key used to store webhook access key.
   */
  protected const WEBHOOK_KEY_STORAGE_KEY = 'druki_git.webhook_key';

  /**
   * The config factory.
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The state key/value storage.
   */
  protected StateInterface $state;

  /**
   * GitSettings constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state key/value storage.
   */
  public function __construct(ConfigFactoryInterface $config_factory, StateInterface $state) {
    $this->configFactory = $config_factory;
    $this->state = $state;
  }

  /**
   * Gets configuration entity.
   *
   * @return \Drupal\Core\Config\Config
   *   The config entity.
   */
  protected function config(): Config {
    return $this->configFactory->getEditable(self::CONFIG_NAME);
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryPath(): ?string {
    return $this->config()->get('repository_path');
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryUrl(): ?string {
    return $this->config()->get('repository_url');
  }

  /**
   * {@inheritdoc}
   */
  public function setRepositoryPath(string $path): GitSettingsInterface {
    if (UrlHelper::isExternal($path)) {
      $message = \sprintf('The repository must be local, %s provided.', $path);
      throw new \InvalidArgumentException($message);
    }
    $this->config()
      ->set('repository_path', $path)
      ->save();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setRepositoryUrl(string $url): GitSettingsInterface {
    if (!UrlHelper::isExternal($url)) {
      $message = \sprintf('The repository URL should be external link, %s provided.', $url);
      throw new \InvalidArgumentException($message);
    }
    $this->config()
      ->set('repository_url', $url)
      ->save();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWebhookAccessKey(): string {
    return $this->state->get(self::WEBHOOK_KEY_STORAGE_KEY);
  }

  /**
   * {@inheritdoc}
   */
  public function setWebhookAccessKey(string $key): GitSettingsInterface {
    $this->state->set(self::WEBHOOK_KEY_STORAGE_KEY, $key);
    return $this;
  }

}
