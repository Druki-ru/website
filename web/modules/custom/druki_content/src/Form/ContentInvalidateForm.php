<?php

declare(strict_types=1);

namespace Drupal\druki_content\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides invalidate form.
 */
final class ContentInvalidateForm extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    /** @var \Drupal\druki_content\Entity\ContentInterface $content */
    $content = $this->getRouteMatch()->getParameter('druki_content');
    return new TranslatableMarkup('Invalidate %title?', [
      '%title' => $content->label(),
    ]);
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
    return 'druki_content_invalidate';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    /** @var \Drupal\druki_content\Entity\ContentInterface $content */
    $content = $this->getRouteMatch()->getParameter('druki_content');
    $content->setSourceHash('invalidated');
    $content->save();
  }

}
