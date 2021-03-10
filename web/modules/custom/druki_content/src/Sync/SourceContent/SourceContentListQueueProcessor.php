<?php

namespace Drupal\druki_content\Sync\SourceContent;

use Drupal\Core\State\StateInterface;
use Drupal\druki_content\Sync\Queue\QueueItemInterface;
use Drupal\druki_content\Sync\Queue\QueueProcessorInterface;

/**
 * Provides source content list queue processor.
 */
final class SourceContentListQueueProcessor implements QueueProcessorInterface {

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The source content parser.
   *
   * @var \Drupal\druki_content\Sync\SourceContent\SourceContentParser
   */
  protected $parser;

  /**
   * The parsed source content loader.
   *
   * @var \Drupal\druki_content\Sync\SourceContent\ParsedSourceContentLoader
   */
  protected $loader;

  /**
   * SourceContentListQueueProcessor constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   * @param \Drupal\druki_content\Sync\SourceContent\SourceContentParser $parser
   *   The source content parser.
   * @param \Drupal\druki_content\Sync\SourceContent\ParsedSourceContentLoader $loader
   *   The parsed source content loader.
   */
  public function __construct(StateInterface $state, SourceContentParser $parser, ParsedSourceContentLoader $loader) {
    $this->state = $state;
    $this->parser = $parser;
    $this->loader = $loader;
  }

  /**
   * {@inheritdoc}
   */
  public function process(QueueItemInterface $item): void {
    $is_force_update = $this->state->get('druki_content.settings.force_update', FALSE);
    /** @var \Drupal\druki_content\Sync\SourceContent\SourceContentList $source_content_list */
    $source_content_list = $item->getPayload();
    foreach ($source_content_list as $source_content) {
      $parsed_source_content = $this->parser->parse($source_content);
      $this->loader->process($parsed_source_content, $is_force_update);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(QueueItemInterface $item): bool {
    return $item instanceof SourceContentListQueueItem;
  }

}
