<?php

namespace Drupal\druki\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a mobile sidebar button block.
 *
 * @Block(
 *   id = "druki_mobile_sidebar_button",
 *   admin_label = @Translation("Mobile sidebar button"),
 *   category = @Translation("Druki")
 * )
 */
final class MobileSidebarButton extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'button',
      '#attributes' => [
        'class' => [
          'js-mobile-sidebar-button',
          'mobile-sidebar-button',
        ],
        'aria-label' => new TranslatableMarkup('Mobile sidebar'),
      ],
      '#attached' => [
        'library' => [
          'druki/mobile-sidebar',
        ],
      ],
    ];
    return $build;
  }

}
