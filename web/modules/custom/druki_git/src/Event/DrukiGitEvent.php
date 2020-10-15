<?php

namespace Drupal\druki_git\Event;

use Drupal\druki_git\Service\GitInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event occurs for git commands.
 */
class DrukiGitEvent extends Event {

  /**
   * The git service instance.
   *
   * @var \Drupal\druki_git\Service\GitInterface
   */
  protected $git;

  /**
   * DrukiGitEvent constructor.
   *
   * @param \Drupal\druki_git\Service\GitInterface $git
   *   The git service.
   */
  public function __construct(GitInterface $git) {
    $this->git = $git;
  }

  /**
   * Gets current instance of Git service.
   *
   * @return \Drupal\druki_git\Service\GitInterface
   *   The git service.
   */
  public function git(): GitInterface {
    return $this->git;
  }

}
