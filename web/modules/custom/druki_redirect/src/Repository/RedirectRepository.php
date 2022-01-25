<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Repository;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\druki_redirect\Data\Redirect;
use Drupal\redirect\Entity\Redirect as RedirectEntity;

/**
 * Provides redirect repository.
 */
final class RedirectRepository implements RedirectRepositoryInterface {

  /**
   * The redirect storage.
   */
  private EntityStorageInterface $redirectStorage;

  /**
   * Constructs a new RedirectRepository object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->redirectStorage = $entity_type_manager->getStorage('redirect');
  }

  /**
   * {@inheritdoc}
   */
  public function findRedirect(Redirect $redirect, string $language = LanguageInterface::LANGCODE_NOT_SPECIFIED): ?int {
    $hash = RedirectEntity::generateHash(
    // @see \Drupal\redirect\Entity\Redirect::setSource().
      \ltrim($redirect->getSource()->getPath(), \DIRECTORY_SEPARATOR),
      $redirect->getSource()->getQuery(),
      $language,
    );

    $redirect_ids = $this->redirectStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('hash', $hash)
      ->condition('language', $language)
      ->condition('druki_redirect', TRUE)
      ->execute();
    return $redirect_ids ? (int) \array_shift($redirect_ids) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function createRedirect(Redirect $redirect, string $language = LanguageInterface::LANGCODE_NOT_SPECIFIED): RedirectEntity {
    $redirect_entity = $this->redirectStorage->create();
    $redirect_entity->setLanguage($language);
    $redirect_entity->setStatusCode(301);
    $redirect_entity->setSource(
      $redirect->getSource()->getPath(),
      $redirect->getSource()->getQuery(),
    );
    $redirect_entity->setRedirect(
      $redirect->getRedirect()->getPath(),
      $redirect->getRedirect()->getQuery(),
      ['fragment' => $redirect->getRedirect()->getFragment()],
    );
    $redirect_entity->set('druki_redirect', TRUE);
    $redirect_entity->save();
    return $redirect_entity;
  }

  /**
   * {@inheritdoc}
   */
  public function loadRedirect(Redirect $redirect, string $language = LanguageInterface::LANGCODE_NOT_SPECIFIED): ?RedirectEntity {
    if ($id = $this->findRedirect($redirect, $language)) {
      return $this->redirectStorage->load($id);
    }
    return NULL;
  }

}
