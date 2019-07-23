<?php

namespace Drupal\druki_git\Service;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\druki_git\Event\DrukiGitEvent;
use Drupal\druki_git\Event\DrukiGitEvents;
use Drupal\druki_git\Exception\GitCommandFailedException;
use Drupal\druki_git\Git\Git as GitUtils;

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
   * The repository path.
   *
   * @var string
   */
  protected $repositoryPath;

  /**
   * The repository realpath.
   *
   * @var string
   */
  protected $repositoryRealpath;

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
  public function __construct(
    ConfigFactoryInterface $config_factory,
    FileSystemInterface $file_system,
    ContainerAwareEventDispatcher $event_dispatcher
  ) {

    $this->configuration = $config_factory->get('druki_git.git_settings');
    $this->fileSystem = $file_system;
    $this->eventDispatcher = $event_dispatcher;

    $this->repositoryPath = $this->configuration->get('repository_path');
    $this->repositoryRealpath = $this->fileSystem->realpath($this->repositoryPath);
  }

  /**
   * {@inheritdoc}
   */
  public function pull(): bool {
    try {
      GitUtils::pull($this->getRepositoryRealpath());

      $event = new DrukiGitEvent($this);
      $this->eventDispatcher->dispatch(DrukiGitEvents::FINISH_PULL, $event);

      return TRUE;
    }
    catch (GitCommandFailedException $e) {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryRealpath(): string {
    return $this->repositoryRealpath;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCommitId(): string {
    return rtrim(GitUtils::getLastCommitId($this->getRepositoryRealpath()));
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryPath(): string {
    return $this->repositoryPath;
  }

  /**
   * {@inheritdoc}
   */
  public function getFileLastCommitId($relative_path): ?string {
    try {
      return rtrim(GitUtils::getFileLastCommitId($relative_path, $this->getRepositoryRealpath()));
    }
    catch (GitCommandFailedException $e) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFileCommitsInfo($relative_path): array {
    return GitUtils::getFileCommitsInfo($relative_path, $this->getRepositoryRealpath());
  }

}
