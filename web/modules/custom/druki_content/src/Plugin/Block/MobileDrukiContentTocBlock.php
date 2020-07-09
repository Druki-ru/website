<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a druki content toc block for mobile phones..
 *
 * @Block(
 *   id = "druki_content_toc_moble",
 *   admin_label = @Translation("Druki content TOC (mobile)"),
 *   category = @Translation("Druki content"),
 *   context_definitions = {
 *     "druki_content" = @ContextDefinition(
 *       "entity:druki_content",
 *       label = @Translation("Druki Content"),
 *       required = TRUE,
 *     )
 *   }
 * )
 */
final class MobileDrukiContentTocBlock extends DrukiContentTocBlock {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = parent::build();

    if (!isset($build['toc'])) {
      return [];
    }

    $build = [
      [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['druki-mobile-toc'],
        ],
        'header' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['druki-mobile-toc__header'],
          ],
          'toggle' => [
            '#type' => 'container',
            '#attributes' => [
              'class' => ['druki-mobile-toc__toggle'],
            ],
            '#markup' => new TranslatableMarkup('Contents'),
          ],
        ],
        'content' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['druki-mobile-toc__content'],
          ],
          'toc' => $build['toc'],
        ],
        '#attached' => [
          'library' => ['druki_content/mobile-toc'],
        ],
      ],
    ];

    return $build;
  }

}
