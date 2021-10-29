<?php

namespace Drupal\druki_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\druki_content\Builder\ContentRenderArrayBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'druki_content_document_render_array' formatter.
 *
 * @FieldFormatter(
 *   id = "druki_content_document_render_array",
 *   label = @Translation("Render array"),
 *   field_types = {
 *     "druki_content_document",
 *   },
 * )
 */
final class ContentDocumentFormatter extends FormatterBase {

  /**
   * The content render array builder.
   */
  protected ContentRenderArrayBuilder $contentRenderArrayBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->contentRenderArrayBuilder = $container->get('druki_content.builder.content_render_array');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    /** @var \Drupal\druki_content\Plugin\Field\FieldType\ContentDocumentItem $item */
    foreach ($items as $delta => $item) {
      /** @var \Drupal\druki_content\Data\ContentDocument $content_document */
      $content_document = $item->get('document')->getContentDocument();
      $content = $content_document->getContent();
      $elements[$delta] = $this->contentRenderArrayBuilder->build($content);
    }

    return $elements;
  }

}
