<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\druki\Data\Contributor;
use Drupal\druki_author\Entity\Author;
use Drupal\druki_author\Entity\AuthorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides contributors formatter with author entity search.
 *
 * @FieldFormatter(
 *   id = "druki_content_contributor",
 *   label = @Translation("Contributor with author"),
 *   field_types = {
 *     "druki_contributor",
 *   },
 * )
 *
 * @todo Remove after ContributorsAndAuthors extra field is complete.
 */
final class ContributorFormatter extends FormatterBase {

  /**
   * The author entity storage.
   */
  protected ContentEntityStorageInterface $authorStorage;

  /**
   * The author entity view builder.
   */
  protected EntityViewBuilderInterface $authorViewBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->authorStorage = $container->get('entity_type.manager')->getStorage('druki_author');
    $instance->authorViewBuilder = $container->get('entity_type.manager')->getViewBuilder('druki_author');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'author_view_mode' => 'default',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    // This array holds all author entities that already processed.
    // E.g. there is three commit for a content:
    // - John Doe <john.doe@example.com>
    // - J.Doe <john.doe@example.com>
    // - John Doe <j.doe@example.com>
    // We assume that John Doe provided both addresses for detection. This is
    // expected but this will generate 3 same contributor 'cards'. To avoid that
    // we store all already processed authors and only process them once.
    // It doesn't matter if this happens for 'anonymous' contributors.
    $processed_authors = [];
    /** @var \Drupal\druki\Plugin\Field\FieldType\ContributorItem $item */
    foreach ($items as $item) {
      $contributor = $item->toContributor();
      $contributor_card = $this->prepareContributor($contributor, $processed_authors);
      if ($contributor_card) {
        $elements[] = $contributor_card;
      }
    }
    return $elements;
  }

  /**
   * Prepares element for a single contributor.
   *
   * @param \Drupal\druki\Data\Contributor $contributor
   *   The contributor value object.
   * @param array $processed_authors
   *   An array with processed authors.
   *
   * @return array|null
   *   A render array with a contributor to display.
   */
  protected function prepareContributor(Contributor $contributor, array &$processed_authors): ?array {
    if ($author = $this->findAuthorForContributor($contributor)) {
      if (\in_array($author->id(), $processed_authors)) {
        return NULL;
      }
      $processed_authors[] = $author->id();
      return $this->buildAuthorItem($author);
    }
    else {
      return $this->buildContributorItem($contributor);
    }
  }

  /**
   * Builds an author entity render array.
   *
   * @param \Drupal\druki_author\Entity\AuthorInterface $author
   *   The author entity.
   *
   * @return array
   *   A render array with author.
   */
  protected function buildAuthorItem(AuthorInterface $author): array {
    return $this->authorViewBuilder->view($author, $this->getSetting('author_view_mode'));
  }

  /**
   * Builds a contributor item.
   *
   * @param \Drupal\druki\Data\Contributor $contributor
   *   A contributor.
   *
   * @return array
   *   A contributor element render array.
   */
  protected function buildContributorItem(Contributor $contributor): array {
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
