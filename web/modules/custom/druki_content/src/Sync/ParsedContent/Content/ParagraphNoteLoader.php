<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provides loader for paragraph entity 'druki_note'.
 */
final class ParagraphNoteLoader extends ParagraphLoaderBase {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = ParagraphNote::class;

  /**
   * {@inheritdoc}
   */
  public function process($data, DrukiContentInterface $content): void {
    $paragraph = $this->paragraphStorage->create(['type' => $data->getParagraphType()]);
    $paragraph->set('druki_note_type', $data->getType());
    $paragraph->set('druki_textarea_formatted', [
      'value' => $data->getContent(),
      'format' => $this->getDefaultTextFilter(),
    ]);

    $this->saveAndAppend($paragraph, $content);
  }

}
