<?php

namespace Drupal\druki\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a header dark mode switcher block.
 *
 * @Block(
 *   id = "druki_header_dark_mode_switcher",
 *   admin_label = @Translation("Header dark mode switcher"),
 *   category = @Translation("Druki")
 * )
 */
final class HeaderDarkModeSwitcher extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [];
    $build['content'] = [
      '#theme' => 'druki_dark_mode_toggle',
    ];
    return $build;
  }

}
