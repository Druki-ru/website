<?php

namespace Drupal\druki_content\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\State;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_content\Synchronization\Queue\QueueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for a druki content entity type.
 */
class DrukiContentSettingsForm extends FormBase {

  /**
   * The state.
   *
   * @var \Drupal\Core\State\State
   */
  protected $state;

  /**
   * The queue.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $queue;

  /**
   * The queue manager.
   *
   * @var \Drupal\druki_content\Synchronization\Queue\QueueManager
   */
  protected $queueManager;

  /**
   * Constructs a new DrukiContentSettingsForm object.
   *
   * @param \Drupal\Core\State\State $state
   *   The state.
   * @param \Drupal\druki_content\Synchronization\Queue\QueueManager $queue_manager
   *   The queue manager.
   */
  public function __construct(State $state, QueueManager $queue_manager) {
    $this->state = $state;
    $this->queue = $queue_manager->queue();
    $this->queueManager = $queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('druki_content.synchronization.queue_manager')
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
    $form = [];
    $form['#tree'] = TRUE;
    $form = $this->buildContentSyncForm($form, $form_state);

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Save settings'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * Builds form for representing settings for content sync.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The modified form.
   */
  protected function buildContentSyncForm(array $form, FormStateInterface $form_state) {
    $form['content_sync'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Update queue'),
    ];

    $form['content_sync']['total'] = [
      '#markup' => '<p>' . new TranslatableMarkup('Current queue items: @count', ['@count' => $this->queue->numberOfItems()]) . '</p>',
    ];

    $form['content_sync']['force'] = [
      '#type' => 'checkbox',
      '#title' => new TranslatableMarkup('Force content processing'),
      '#description' => new TranslatableMarkup('Content will be processed even if it not updated from last queue.'),
      '#default_value' => $this->state->get('druki_content.settings.force_update', FALSE),
    ];

    $form['content_sync']['actions'] = ['#type' => 'actions'];
    $form['content_sync']['actions']['run'] = [
      '#type' => 'submit',
      '#button_type' => 'secondary',
      '#value' => new TranslatableMarkup('Run queue'),
      '#submit' => [[$this->queueManager, 'run']],
    ];

    $form['content_sync']['actions']['clear'] = [
      '#type' => 'submit',
      '#button_type' => 'danger',
      '#value' => new TranslatableMarkup('Clear queue'),
      '#submit' => [[$this->queueManager, 'clear']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->state->set('druki_content.settings.force_update', $form_state->getValue([
      'content_sync',
      'force',
    ]));
  }

}
