<?php

declare(strict_types=1);

namespace Drupal\druki\Data;

/**
 * Provides value-object with contributor information.
 */
final class Contributor {

  /**
   * The contributor username.
   */
  protected string $username;

  /**
   * The contributor email.
   */
  protected string $email;

  /**
   * Constructs a new Contributor object.
   *
   * @param string $username
   *   The contributor username.
   * @param string $email
   *   The contributor email.
   */
  public function __construct(string $username, string $email) {
    $this->username = $username;
    $this->email = $email;
  }

  /**
   * Gets contributor username.
   *
   * @return string
   *   The username.
   */
  public function getUsername(): string {
    return $this->username;
  }

  /**
   * Gets contributor email.
   *
   * @return string
   *   The email.
   */
  public function getEmail(): string {
    return $this->email;
  }

}
