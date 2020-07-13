<?php

namespace Drupal\druki_git\Event;

use Drupal\druki_git\Service\GitInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event occurs for git commands.
 *
 * @phpstan-ignore-next-line
 *   The Drupal doesn't allow to use new object for now.
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
   */
  public function git(): GitInterface {
    return $this->git;
  }

}
