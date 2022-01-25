<?php

declare(strict_types=1);

namespace Drupal\druki_content\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;

/**
 * Provides an implementation for 'druki_content' entity settings.
 */
final class ContentSettings implements ContentSettingsInterface {

  /**
   * The collection with entity settings stored in key/value storage.
   */
  protected const KEY_VALUE_COLLECTION_KEY = 'druki_content_settings';

  /**
   * The key/value store for content entity.
   */
  protected KeyValueStoreInterface $keyValueStore;

  /**
   * Constructs a new ContentSettings object.
   *
   * @param \Drupal\Core\KeyValueStore\KeyValueFactoryInterface $key_value_factory
   *   The key/value factory.
   */
  public function __construct(KeyValueFactoryInterface $key_value_factory) {
    $this->keyValueStore = $key_value_factory->get(self::KEY_VALUE_COLLECTION_KEY);
  }

  /**
   * {@inheritdoc}
   */
  public function getContentSourceUri(): ?string {
    return $this->keyValueStore->get('content_source_uri');
  }

  /**
   * {@inheritdoc}
   */
  public function setContentSourceUri(string $uri): ContentSettingsInterface {
    $uri = \rtrim($uri, DIRECTORY_SEPARATOR);
    $this->keyValueStore->set('content_source_uri', $uri);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryUrl(): ?string {
    return $this->keyValueStore->get('repository_url');
  }

  /**
   * {@inheritdoc}
   */
  public function setRepositoryUrl(string $url): ContentSettingsInterface {
    $this->keyValueStore->set('repository_url', $url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContentUpdateWebhookAccessKey(): ?string {
    return $this->keyValueStore->get('webhook_url');
  }

  /**
   * {@inheritdoc}
   */
  public function setContentUpdateWebhookAccessKey(string $access_key): ContentSettingsInterface {
    $this->keyValueStore->set('webhook_url', $access_key);
    return $this;
  }

}
