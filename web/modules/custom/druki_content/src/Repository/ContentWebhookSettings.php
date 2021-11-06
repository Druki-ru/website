<?php

declare(strict_types=1);

namespace Drupal\druki_content\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;

/**
 * Provides content webhook settings repository.
 */
final class ContentWebhookSettings implements ContentWebhookSettingsInterface {

  /**
   * The storage collection key.
   */
  protected const COLLECTION_KEY = 'druki_content.webhook_settings';

  /**
   * The key/value collection.
   */
  protected KeyValueStoreInterface $storage;

  /**
   * Constructs a new ContentWebhookSettings object.
   *
   * @param \Drupal\Core\KeyValueStore\KeyValueFactoryInterface $key_value_factory
   *   The key/value factory.
   */
  public function __construct(KeyValueFactoryInterface $key_value_factory) {
    $this->storage = $key_value_factory->get(self::COLLECTION_KEY);
  }

  /**
   * {@inheritdoc}
   */
  public function getContentUpdateWebhookAccessKey(): string {
    return $this->storage->get('content_update_access_key');
  }

  /**
   * {@inheritdoc}
   */
  public function setContentUpdateWebhookAccessKey(string $access_key): ContentWebhookSettingsInterface {
    $this->storage->set('content_update_access_key', $access_key);
    return $this;
  }

}
