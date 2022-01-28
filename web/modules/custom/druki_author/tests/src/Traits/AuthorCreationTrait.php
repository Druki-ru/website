<?php

namespace Drupal\Tests\druki_author\Traits;

use Drupal\druki_author\Entity\Author;
use Drupal\druki_author\Entity\AuthorInterface;
use Drupal\Tests\RandomGeneratorTrait;
use weitzman\DrupalTestTraits\DrupalTrait;

/**
 * Provides methods to create druki_author entities.
 */
trait AuthorCreationTrait {

  use RandomGeneratorTrait;
  use DrupalTrait;

  /**
   * Creates an Author.
   *
   * @param array $values
   *   An associative array with values for entity.
   *
   * @return \Drupal\druki_author\Entity\AuthorInterface
   *   A created entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createAuthor(array $values = []): AuthorInterface {
    $values += [
      'id' => $this->randomMachineName(32),
      'name_given' => $this->randomString(),
      'name_family' => $this->randomString(),
      'country' => 'RU',
    ];

    $author = Author::create($values);
    $author->save();

    $this->markEntityForCleanup($author);

    return $author;
  }

}
