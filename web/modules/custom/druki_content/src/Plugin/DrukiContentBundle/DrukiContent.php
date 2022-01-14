<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\DrukiContentBundle;

use Drupal\Component\Plugin\PluginBase;
use Drupal\druki_content\Annotation\DrukiContentBundle;

/**
 * Provides 'druki_content' bundle type.
 *
 * @todo Remove it after deploying 'druki_content_update_9302'. This bundle is
 *   fallback for proper schema update.
 *
 * @DrukiContentBundle(
 *   id = "druki_content",
 *   label = @Translation("Delete"),
 * )
 */
final class DrukiContent extends PluginBase implements DrukiContentBundleInterface {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions(): array {
    return [];
  }

}
