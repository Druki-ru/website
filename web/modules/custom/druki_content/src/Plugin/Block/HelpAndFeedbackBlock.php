<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\druki_content\Entity\DrukiContentInterface;
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
   * The remote repository URL.
   *
   * @var string
   */
  protected $repositoryUrl;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $config_factory = $container->get('config.factory');
    $instance->repositoryUrl = $config_factory->get('druki_git.git_settings')->get('repository_url');

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
    $improve_title = new TranslatableMarkup('Feedback: @title', [
      '@title' => $this->getEntityFromContext()->label(),
    ]);

    return [
      '#theme' => 'druki_content_help_and_feedback',
      '#edit_url' => $this->getEntityFromContext()->toUrl('edit-remote'),
      '#improve_url' => Url::fromUri($this->repositoryUrl . '/issues/new', [
        'query' => [
          'title' => $improve_title,
          'body' => $this->buildImproveBody(),
          'labels' => 'improvement',
        ],
      ]),
      '#help_url' => Url::fromUri($this->repositoryUrl . '/discussions/new'),
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
      '- **External ID:** ' . $this->getEntityFromContext()->getExternalId(),
      '- **Core (if set):** ' . $this->getEntityFromContext()->getCore(),
      '- **Relative pathname:** ' . $this->getEntityFromContext()->getRelativePathname(),
      '- **Sync timestamp:** ' . $this->getEntityFromContext()->getSyncTimestamp(),
    ];

    return implode(PHP_EOL, $pieces);
  }

}
