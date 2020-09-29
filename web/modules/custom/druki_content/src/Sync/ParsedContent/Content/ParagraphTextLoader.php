<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provides loader for paragraph entity 'druki_text'.
 */
final class ParagraphTextLoader extends ParagraphLoaderBase {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = ParagraphText::class;

  /**
   * {@inheritdoc}
   */
  public function process($data, DrukiContentInterface $content): void {
    $paragraph = $this->getParagraphStorage()->create(['type' => $data->getParagraphType()]);
    $paragraph->set('druki_textarea_formatted', [
      'value' => $data->getContent(),
      'format' => $this->getDefaultTextFilter(),
    ]);

    $this->saveAndAppend($paragraph, $content);
  }

}
