<?php

namespace Drupal\druki\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a header dark mode switcher block.
 *
 * @Block(
 *   id = "druki_header_dark_mode_switcher",
 *   admin_label = @Translation("Header dark mode switcher"),
 *   category = @Translation("Druki")
 * )
 */
class HeaderDarkModeSwitcher extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#type' => 'inline_template',
      '#template' => '<div class="header-dark-mode-switcher js-dark-mode-switcher"></div>',
    ];

    return $build;
  }

}
