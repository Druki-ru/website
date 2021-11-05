<?php

namespace Drupal\druki_content\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\druki_content\Repository\DrukiContentStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a confirmation form before clearing out the examples.
 */
final class DrukiContentDeleteAllForm extends ConfirmFormBase {

  /**
   * The druki content storage.
   */
  protected DrukiContentStorage $drukiContentStorage;

  /**
   * DrukiContentDeleteAllForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container): object {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_content_delete_all';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): string {
    return $this->t('Are you sure you want to do this?');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $druki_content = $this->drukiContentStorage->loadMultiple();
    $this->drukiContentStorage->delete($druki_content);

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('entity.druki_content.collection');
  }

}
