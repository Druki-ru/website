<?php

declare(strict_types=1);

namespace Drupal\druki_content\Repository;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides an object with content source settings.
 */
final class ContentSourceSettings implements ContentSourceSettingsInterface {

  /**
   * The config name.
   */
  protected const CONFIG_NAME = 'druki_content.content_source_settings';

  /**
   * The config factory.
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * Constructs a new ContentSourceSettings object.
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
  public function getRepositoryUri(): ?string {
    return $this->config()->get('repository_uri');
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
  public function getRepositoryUrl(): ?string {
    return $this->config()->get('repository_url');
  }

  /**
   * {@inheritdoc}
   */
  public function setRepositoryUri(string $uri): ContentSourceSettingsInterface {
    if (UrlHelper::isExternal($uri)) {
      $message = \sprintf('The repository must be local, %s provided.', $uri);
      throw new \InvalidArgumentException($message);
    }
    $this->config()
      ->set('repository_uri', $uri)
      ->save();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setRepositoryUrl(string $url): ContentSourceSettingsInterface {
    if (!UrlHelper::isExternal($url)) {
      $message = \sprintf('The repository URL should be external link, %s provided.', $url);
      throw new \InvalidArgumentException($message);
    }
    $this->config()
      ->set('repository_url', $url)
      ->save();
    return $this;
  }

}
