<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Repository;

use Drupal\Core\Language\LanguageInterface;
use Drupal\druki_redirect\Data\Redirect;
use Drupal\redirect\Entity\Redirect as RedirectEntity;

/**
 * Provides interface for custom implementation of redirect repository.
 */
interface RedirectRepositoryInterface {

  /**
   * Finds redirect entity based on source and redirect URL.
   *
   * @param \Drupal\druki_redirect\Data\Redirect $redirect
   *   The redirect information.
   * @param string $language
   *   The language.
   *
   * @return int|null
   *   The redirect ID.
   */
  public function findRedirect(Redirect $redirect, string $language = LanguageInterface::LANGCODE_NOT_SPECIFIED): ?int;

  /**
   * Creates redirect from value object.
   *
   * @param \Drupal\druki_redirect\Data\Redirect $redirect
   *   The redirect value object.
   * @param string $language
   *   The redirect language.
   *
   * @return \Drupal\redirect\Entity\Redirect
   *   The created redirect entity.
   */
  public function createRedirect(Redirect $redirect, string $language = LanguageInterface::LANGCODE_NOT_SPECIFIED): RedirectEntity;

  /**
   * Loads redirect entity by value object.
   *
   * @param \Drupal\druki_redirect\Data\Redirect $redirect
   *   The redirect object.
   * @param string $language
   *   The redirect language.
   *
   * @return \Drupal\redirect\Entity\Redirect|null
   *   The redirect entity. NULL if not found.
   */
  public function loadRedirect(Redirect $redirect, string $language = LanguageInterface::LANGCODE_NOT_SPECIFIED): ?RedirectEntity;

}
