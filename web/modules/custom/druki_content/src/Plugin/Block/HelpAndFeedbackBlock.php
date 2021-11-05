<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_git\Git\GitSettingsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides help and feedback block.
 *
 * @Block(
 *   id = "druki_content_help_and_feedback",
 *   admin_label = @Translation("Druki Help and Feedback"),
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
final class HelpAndFeedbackBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The git settings.
   */
  protected GitSettingsInterface $gitSettings;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->gitSettings = $container->get('druki_git.settings');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'context_mapping' => [
        'druki_content' => '@druki_content.druki_content_route_context:druki_content',
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * Gets entity from context.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The content entity.
   */
  private function getEntityFromContext(): DrukiContentInterface {
    return $this->getContextValue('druki_content');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $repository_url = $this->gitSettings->getRepositoryUrl();
    $improve_title = new TranslatableMarkup('Feedback: @title', [
      '@title' => $this->getEntityFromContext()->label(),
    ]);

    return [
      '#theme' => 'druki_content_help_and_feedback',
      '#edit_url' => $this->getEntityFromContext()->toUrl('edit-remote'),
      '#improve_url' => Url::fromUri($repository_url . '/issues/new', [
        'query' => [
          'title' => $improve_title,
          'body' => $this->buildImproveBody(),
          'labels' => 'improvement',
        ],
      ]),
      '#help_url' => Url::fromUri($repository_url . '/discussions/new', [
        'query' => [
          'category' => 'Help',
        ],
      ]),
    ];
  }

  /**
   * Builds content for improvements request.
   *
   * @return string
   *   The body value.
   */
  private function buildImproveBody(): string {
    $pieces = [
      new TranslatableMarkup('Describe what is wrong.'),
      '',
      '---',
      '',
      '- **Entity ID:** ' . $this->getEntityFromContext()->id(),
      '- **Slug:** ' . $this->getEntityFromContext()->getSlug(),
      '- **Core (if set):** ' . $this->getEntityFromContext()->getCore(),
      '- **Relative pathname:** ' . $this->getEntityFromContext()->getRelativePathname(),
    ];

    return \implode(\PHP_EOL, $pieces);
  }

}
