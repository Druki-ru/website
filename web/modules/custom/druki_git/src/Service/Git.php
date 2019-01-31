<?php

namespace Drupal\druki_git\Service;

use Cz\Git\GitRepository;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigValueException;

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
   * Git constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configuration = $config_factory->get('druki_git.git_settings');

    $repository_url = $this->configuration->get('repository_url');
    if (!$repository_url) {
      throw new ConfigValueException('The repository url is not set.');
    }

    $repository_path = $this->configuration->get('repository_path');
    if (!$repository_path) {
      throw new ConfigValueException('The repository path is not set.');
    }

    $this->git = new GitRepository($repository_path);
  }

  public function pull() {
    $this->git->pull('public://druki-git/content');
  }

}
