<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Data;

use Drupal\Component\Utility\UrlHelper;
use Drupal\redirect\Entity\Redirect as RedirectEntity;

/**
 * Provides value object with redirect information.
 */
final class Redirect {

  /**
   * The source URL.
   */
  private RedirectUrl $source;

  /**
   * The redirect URL.
   */
  private RedirectUrl $redirect;

  /**
   * Constructs a new Redirect object.
   *
   * @param \Drupal\druki_redirect\Data\RedirectUrl $source
   *   The source URL.
   * @param \Drupal\druki_redirect\Data\RedirectUrl $redirect
   *   The redirect (destination) URL.
   */
  public function __construct(RedirectUrl $source, RedirectUrl $redirect) {
    $this->source = $source;
    $this->redirect = $redirect;
  }

  /**
   * Creates redirect object from user-entered values.
   *
   * @param string $source_path
   *   The source path.
   * @param string $redirect_path
   *   The redirect path.
   *
   * @return self
   *   The new instace of Redirect.
   */
  public static function createFromUserInput(string $source_path, string $redirect_path): self {
    $source_url = RedirectUrl::createFromUserInput($source_path);
    $redirect_url = RedirectUrl::createFromUserInput($redirect_path);
    return new self($source_url, $redirect_url);
  }

  /**
   * Creates redirect value object from existing entity.
   *
   * @param \Drupal\redirect\Entity\Redirect $entity
   *   The redirect entity.
   *
   * @return self
   *   The instance of Redirect.
   */
  public static function createFromRedirectEntity(RedirectEntity $entity): self {
    $source = $entity->getSource() + ['query' => []];
    $source_url = new RedirectUrl($source['path'], $source['query']);

    $redirect = UrlHelper::parse($entity->getRedirectUrl()->toString());
    $redirect_url = new RedirectUrl($redirect['path'], $redirect['query'], $redirect['fragment']);

    return new self($source_url, $redirect_url);
  }

  /**
   * Generates checksum for current redirect.
   *
   * @return string
   *   The checksum.
   */
  public function checksum(): string {
    $source_query = $this->getSource()->getQuery();
    \ksort($source_query);

    $redirect_query = $this->getRedirect()->getQuery();
    \ksort($redirect_query);

    $checksum_parts = [
      \ltrim($this->getSource()->getPath(), '/'),
      \serialize($source_query),
      $this->getSource()->getFragment(),
      \ltrim($this->getRedirect()->getPath(), '/'),
      \serialize($redirect_query),
      $this->getRedirect()->getFragment(),
    ];

    return \md5(\implode(':', $checksum_parts));
  }

  /**
   * Gets source URL.
   *
   * @return \Drupal\druki_redirect\Data\RedirectUrl
   *   The URL object.
   */
  public function getSource(): RedirectUrl {
    return $this->source;
  }

  /**
   * Gets redirect URL.
   *
   * @return \Drupal\druki_redirect\Data\RedirectUrl
   *   The URL object.
   */
  public function getRedirect(): RedirectUrl {
    return $this->redirect;
  }

}
