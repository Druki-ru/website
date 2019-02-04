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
  public function __construct(ConfigFactoryInterface $config_factory, FileSystemInterface $file_system, ContainerAwareEventDispatcher $event_dispatcher) {
    $this->configuration = $config_factory->get('druki_git.git_settings');
    $this->fileSystem = $file_system;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->repositoryPath = $this->configuration->get('repository_path');
    // Git library don't detect stream wrappers, so we need to convert our uri
    // to real valid path.
    $this->repositoryRealpath = $this->fileSystem->realpath($this->repositoryPath);

    try {
      $this->git = new GitRepository($this->repositoryRealpath);

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

  /**
   * {@inheritdoc}
   */
  public function getRepositoryPath() {
    return $this->repositoryPath;
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositoryRealpath() {
    return $this->repositoryRealpath;
  }

  /**
   * {@inheritdoc}
   */
  public function getFileLastCommitId($relative_path) {
    $commit_hash = $this->git->execute([
      'command' => [
        // This is actually bad. @todo write own execute method, because from
        // library is pretty bad.
        'log --pretty=format:%H -- '.  $relative_path => '-n 1',
      ],
    ]);

    if (preg_match('/^[0-9a-f]{40}$/i', $commit_hash[0])) {
      return $commit_hash[0];
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFileCommitsInfo($relative_path) {
    $result = $this->git->execute([
      'command' => [
        'shortlog -sen -- ' . $relative_path => 'filler',
      ],
    ]);

    $commits_info = [];
    foreach ($result as $item) {
      preg_match_all("/(\d+)\s(.+)\s<([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)>/", $item, $matches);

      $commits_info[] = [
        'count' => $matches[1][0],
        'name' => $matches[2][0],
        'email' => $matches[3][0],
      ];
    }

    return $commits_info;
  }

}
