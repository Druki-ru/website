<?php

namespace Drupal\druki_git\Service;

use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\druki_git\Event\DrukiGitEvent;
use Drupal\druki_git\Event\DrukiGitEvents;

/**
 * Service wrapper to git library.
 *
 * @package Drupal\druki_git\Service
 */
class Git implements GitInterface {

  /**
   * The configuration object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $configuration;

  /**
   * The git repository.
   *
   * @var \Cz\Git\GitRepository
   */
  protected $git;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The event dispatcher.
   *
   * @var \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected $eventDispatcher;

  /**
   * Git constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   * @param \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(ConfigFactoryInterface $config_factory, FileSystemInterface $file_system, ContainerAwareEventDispatcher $event_dispatcher) {
    $this->configuration = $config_factory->get('druki_git.git_settings');
    $this->fileSystem = $file_system;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function init() {
    $repository_path = $this->configuration->get('repository_path');
    // Git library don't detect stream wrappers, so we need to convert our uri
    // to real valid path.
    $repository_realpath = $this->fileSystem->realpath($repository_path);

    try {
      $this->git = new GitRepository($repository_realpath);

      return $this;
    }
    catch (GitException $e) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function pull() {
    try {
      $this->git->pull();

      // Fire event.
      $event = new DrukiGitEvent($this);
      $this->eventDispatcher->dispatch(DrukiGitEvents::FINISH_PULL, $event);

      return $this;
    }
    catch (GitException $e) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCommitId() {
    return $this->git->getLastCommitId();
  }

}
