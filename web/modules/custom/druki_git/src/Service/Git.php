<?php

namespace Drupal\druki_git\Service;

use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;

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
   * Git constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   */
  public function __construct(ConfigFactoryInterface $config_factory, FileSystemInterface $file_system) {
    $this->configuration = $config_factory->get('druki_git.git_settings');
    $this->fileSystem = $file_system;
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
