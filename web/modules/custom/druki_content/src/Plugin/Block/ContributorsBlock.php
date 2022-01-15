<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a druki content contributors list.
 *
 * @Block(
 *   id = "druki_content_contributors",
 *   admin_label = @Translation("Contributors"),
 *   category = @Translation("Druki content"),
 *   context_definitions = {
 *     "druki_content" = @ContextDefinition("entity:druki_content", label = @Translation("Druki Content"), required = TRUE)
 *   }
 * )
 */
final class ContributorsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'context_mapping' => [
        'druki_content' => '@druki_content.druki_content_route_context:druki_content',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    /** @var \Drupal\druki_content\Entity\ContentInterface $content */
    $content = $this->getContextValue('druki_content');
    if ($content->get('contributors')->isEmpty()) {
      return [];
    }
    return [
      $content->get('contributors')->view([
        'type' => 'druki_content_contributor',
        'label' => 'hidden',
        'settings' => [
          'author_view_mode' => 'content_contributor',
        ],
      ]),
    ];
  }

}
