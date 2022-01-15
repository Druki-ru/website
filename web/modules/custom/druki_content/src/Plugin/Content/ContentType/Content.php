<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\Content\ContentType;

use Drupal\Component\Plugin\PluginBase;

/**
 * Provides 'druki_content' bundle type.
 *
 * @todo Remove it after deploying 'druki_content_update_9302'. This bundle is
 *   fallback for proper schema update.
 *
 * @ContentType(
 *   id = "druki_content",
 *   label = @Translation("Delete"),
 * )
 */
final class Content extends PluginBase implements ContentTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions(): array {
    return [];
  }

}
