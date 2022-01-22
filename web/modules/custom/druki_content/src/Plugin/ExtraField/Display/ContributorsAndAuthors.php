<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\druki\Data\Contributor;
use Drupal\druki_author\Entity\Author;
use Drupal\druki_author\Entity\AuthorInterface;
use Drupal\druki_content\Entity\ContentInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an extra field to display contributors and authors.
 *
 * @ExtraFieldDisplay(
 *   id = "contributors_and_authors",
 *   label = @Translation("Contributors & Authors"),
 *   visible = TRUE,
 *   bundles = {
 *     "druki_content.documentation",
 *   },
 * )
 */
final class ContributorsAndAuthors extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  /**
   * An array with processed author IDs to exclude duplicates.
   */
  protected array $processedAuthorIds = [];

  /**
   * The author storage.
   */
  protected ContentEntityStorageInterface $authorStorage;

  /**
   * The author view builder.
   */
  protected EntityViewBuilderInterface $authorViewBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->authorStorage = $container->get('entity_type.manager')->getStorage('druki_author');
    $instance->authorViewBuilder = $container->get('entity_type.manager')->getViewBuilder('druki_author');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity): array {
    \assert($entity instanceof ContentInterface);
    $elements = [];

    if ($entity->hasAuthors()) {
      $teasers = $this->prepareAuthors($entity);
      if ($teasers) {
        foreach ($teasers as $teaser) {
          $elements[] = $teaser;
        }
      }
    }

    if ($entity->hasContributors()) {
      $teasers = $this->prepareContributors($entity);
      if ($teasers) {
        foreach ($teasers as $teaser) {
          $elements[] = $teaser;
        }
      }
    }

    return $elements;
  }

  /**
   * Prepares teasers for author references.
   *
   * @param \Drupal\druki_content\Entity\ContentInterface $content
   *   A content entity.
   *
   * @return array
   *   An array with teaser render arrays.
   */
  protected function prepareAuthors(ContentInterface $content): array {
    $teasers = [];
    foreach ($content->getAuthors() as $author) {
      $this->processedAuthorIds[] = $author->id();
      $teasers[] = $this->buildAuthor($author);
    }
    return $teasers;
  }

  /**
   * Builds an author element.
   *
   * @param \Drupal\druki_author\Entity\AuthorInterface $author
   *   An author entity.
   *
   * @return array
   *   A render array with an element.
   */
  protected function buildAuthor(AuthorInterface $author): array {
    return $this->authorViewBuilder->view($author, 'content_contributor');
  }

  /**
   * Prepares teasers for contributors.
   *
   * @param \Drupal\druki_content\Entity\ContentInterface $content
   *   A content entity.
   *
   * @return array
   *   An array with teaser render arrays.
   */
  protected function prepareContributors(ContentInterface $content): array {
    $teasers = [];
    /** @var \Drupal\druki\Data\Contributor $contributor */
    foreach ($content->getContributors() as $contributor) {
      $teasers[] = $this->buildContributor($contributor);
    }
    return $teasers;
  }

  /**
   * Builds a contributor element.
   *
   * @param \Drupal\druki\Data\Contributor $contributor
   *   A contributor information.
   *
   * @return array|null
   *   A render array with an element.
   */
  protected function buildContributor(Contributor $contributor): ?array {
    if ($author = $this->findAuthorForContributor($contributor)) {
      if (\in_array($author->id(), $this->processedAuthorIds)) {
        return NULL;
      }
      $this->processedAuthorIds[] = $author->id();
      return $this->buildAuthor($author);
    }
    else {
      return [
        '#type' => 'druki_avatar_placeholder',
        '#username' => $contributor->getUsername(),
        '#attributes' => [
          'data-druki-selector' => 'contributor-hovercard',
          'data-hovercard-username' => $contributor->getUsername(),
        ],
        '#attached' => [
          'library' => [
            'druki_content/contributor-hovercard',
          ],
        ],
      ];
    }
  }

  /**
   * Finds an author entity for contributor.
   *
   * @return \Drupal\druki_author\Entity\Author|null
   *   The author entity if found.
   */
  protected function findAuthorForContributor(Contributor $contributor): ?Author {
    $author_ids = $this->authorStorage->getQuery()
      ->condition('identification.type', 'email')
      ->condition('identification.value', $contributor->getEmail())
      ->range(0, 1)
      ->execute();

    if (empty($author_ids)) {
      return NULL;
    }

    $author_id = \reset($author_ids);
    return $this->authorStorage->load($author_id);
  }

}
