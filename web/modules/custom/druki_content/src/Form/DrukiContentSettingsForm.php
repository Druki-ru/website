<?php

namespace Drupal\druki_content\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Queue\QueueInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for a druki content entity type.
 */
class DrukiContentSettingsForm extends FormBase {

  /**
   * The queue of processing content.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $queue;

  /**
   * DrukiContentSettingsForm constructor.
   */
  public function __construct(QueueInterface $queue) {
    $this->queue = $queue;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('queue')->get('druki_content_processing')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'druki_content_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['update_queue'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Update queue'),
    ];

    $form['update_queue']['total'] = [
      '#markup' => $this->t('Current queue items: @count', ['@count' => $this->queue->numberOfItems()]),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
