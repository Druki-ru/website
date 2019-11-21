<?php

namespace Drupal\druki\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a header branding navigation block.
 *
 * @Block(
 *   id = "druki_header_branding_navigation",
 *   admin_label = @Translation("Header branding navigation"),
 *   category = @Translation("Druki")
 * )
 */
class HeaderBrandingNavigationBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#theme' => 'druki_header_branding_navigation',
    ];
    return $build;
  }

}
