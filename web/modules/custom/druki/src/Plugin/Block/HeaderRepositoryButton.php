<?php

namespace Drupal\druki\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides a project repository button block.
 *
 * @Block(
 *   id = "druki_header_repository_button",
 *   admin_label = @Translation("Header repository button"),
 *   category = @Translation("Druki")
 * )
 */
final class HeaderRepositoryButton extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#type' => 'link',
      '#url' => Url::fromUri('https://github.com/Druki-ru'),
      '#title' => NULL,
      '#attributes' => [
        'class' => [
          'header-repository-button',
        ],
        'target' => '_blank',
        'rel' => 'noopener nofollow',
        'title' => new TranslatableMarkup('Project repository'),
        'aria-label' => new TranslatableMarkup('Project repository'),
      ],
    ];
    return $build;
  }

}
