<?php

declare(strict_types=1);

namespace Drupal\druki_content\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\druki_content\Batch\ContentInvalidateAllBatch;

/**
 * Provides invalidate form.
 */
final class ContentInvalidateAllForm extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    return new TranslatableMarkup('Invalidate all content?');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): TranslatableMarkup {
    return new TranslatableMarkup('Invalidates content nad guarantees that content will be fully updated on next sync.');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return Url::fromRoute('entity.druki_content.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_content_invalidate_all';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $batch = ContentInvalidateAllBatch::build();
    \batch_set($batch->toArray());
  }

}
