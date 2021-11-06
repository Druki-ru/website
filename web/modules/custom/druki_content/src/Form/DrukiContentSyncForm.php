<?php

namespace Drupal\druki_content\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\State\State;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_content\Queue\ContentSyncQueueManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for a druki content entity type.
 *
 * @todo Refactor that form.
 */
final class DrukiContentSyncForm extends FormBase {

  /**
   * The state.
   */
  protected State $state;

  /**
   * The queue.
   */
  protected QueueInterface $queue;

  /**
   * The queue manager.
   */
  protected ContentSyncQueueManagerInterface $queueManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = new static();
    $instance->state = $container->get('state');
    $instance->queue = $container->get('queue')->get(ContentSyncQueueManagerInterface::QUEUE_NAME);
    $instance->queueManager = $container->get('druki_content.queue.content_sync_manager');
    return $instance;
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
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = [];
    $form['#tree'] = TRUE;
    $form = $this->buildQueueBuilderForm($form, $form_state);
    $form = $this->buildQueueManagerForm($form, $form_state);

    $form['force_sync'] = [
      '#type' => 'checkbox',
      '#title' => new TranslatableMarkup('Force content processing'),
      '#description' => new TranslatableMarkup('Content will be processed even if it not updated from last queue.'),
      '#default_value' => $this->state->get('druki_content.settings.force_update', FALSE),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Save settings'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * Builds element to build queue.
   *
   * @param array $form
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The modified form.
   */
  protected function buildQueueBuilderForm(array $form, FormStateInterface $form_state): array {
    $form['queue_builder'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Queue builder'),
    ];

    $form['queue_builder']['git'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Build from Git'),
      '#description' => new TranslatableMarkup('The "git pull" command will be invoked, which will trigger event and build queue from it.'),
    ];

    $form['queue_builder']['git']['build'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => new TranslatableMarkup('Pull from Git'),
      '#submit' => [[$this, 'createQueueFromGit']],
    ];

    $form['queue_builder']['folder'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Build from folder'),
    ];

    $form['queue_builder']['folder']['uri'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('URI'),
      '#description' => new TranslatableMarkup('The URI with source content.'),
    ];

    $form['queue_builder']['folder']['build'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => new TranslatableMarkup('Create'),
      '#submit' => [[$this, 'createQueueFromFolder']],
    ];

    return $form;
  }

  /**
   * Builds control elements for queue manager.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The modified form.
   */
  protected function buildQueueManagerForm(array $form, FormStateInterface $form_state): array {
    $form['queue_manager'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Manage queue'),
    ];

    $form['queue_manager']['total'] = [
      '#markup' => '<p>' . new TranslatableMarkup('Current queue items: @count', ['@count' => $this->queue->numberOfItems()]) . '</p>',
    ];

    $form['queue_manager']['actions'] = ['#type' => 'actions'];
    $form['queue_manager']['actions']['run'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => new TranslatableMarkup('Run queue'),
      '#submit' => [[$this, 'runQueue']],
    ];

    $form['queue_manager']['actions']['clear'] = [
      '#type' => 'submit',
      '#button_type' => 'danger',
      '#value' => new TranslatableMarkup('Clear queue'),
      '#submit' => [[$this, 'clearQueue']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->state->set('druki_content.settings.force_update', $form_state->getValue('force_sync'));
  }

  /**
   * Runs queue worker.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function runQueue(): void {
    $this->queueManager->run();
  }

  /**
   * Clear queue from all items.
   */
  public function clearQueue(): void {
    $this->queueManager->delete();
  }

  /**
   * Builds new queue via Git pull.
   */
  public function createQueueFromGit(): void {
    // @todo Replace.
  }

  /**
   * Builds new queue via Git pull.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function createQueueFromFolder(array $form, FormStateInterface $form_state): void {
    $uri = $form_state->getValue(['queue_builder', 'folder', 'uri']);
    if (!\is_dir($uri)) {
      return;
    }

    $this->queueManager->buildFromPath($uri);
  }

}
