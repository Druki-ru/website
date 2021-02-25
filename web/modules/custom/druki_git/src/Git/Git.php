<?php

namespace Drupal\druki_git\Git;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\File\FileSystemInterface;
use Drupal\druki_git\Event\DrukiGitEvent;
use Drupal\druki_git\Event\DrukiGitEvents;
use Drupal\druki_git\Exception\GitCommandFailedException;

/**
 * Provides git service.
 */
final class Git implements GitInterface {

  /**
   * The git settings.
   *
   * @var \Drupal\druki_git\Git\GitSettingsInterface
   */
  protected $gitSettings;

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
   * Git constructor.
   *
   * @param \Drupal\druki_git\Git\GitSettingsInterface $git_settings
   *   The git settings.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   * @param \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(GitSettingsInterface $git_settings, FileSystemInterface $file_system, ContainerAwareEventDispatcher $event_dispatcher) {
    $this->gitSettings = $git_settings;
    $this->fileSystem = $file_system;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function pull(): bool {
    try {
      GitHelper::pull($this->getRepositoryRealpath());

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
    return $this->fileSystem->realpath($this->gitSettings->getRepositoryPath());
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCommitId(): string {
    return \rtrim(GitHelper::getLastCommitId($this->getRepositoryRealpath()));
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryPath(): string {
    return $this->gitSettings->getRepositoryPath();
  }

  /**
   * {@inheritdoc}
   */
  public function getFileLastCommitId($relative_path): ?string {
    try {
      return \rtrim(GitHelper::getFileLastCommitId($relative_path, $this->getRepositoryRealpath()));
    }
    catch (GitCommandFailedException $e) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFileCommitsInfo($relative_path): array {
    return GitHelper::getFileCommitsInfo($relative_path, $this->getRepositoryRealpath());
  }

}
