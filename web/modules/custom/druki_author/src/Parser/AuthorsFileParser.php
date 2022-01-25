<?php

declare(strict_types=1);

namespace Drupal\druki_author\Parser;

use Drupal\Component\Serialization\Json;
use Drupal\druki_author\Data\Author;
use Drupal\druki_author\Data\AuthorList;
use Drupal\druki_author\Data\AuthorsFile;

/**
 * Provides authors file parser.
 */
final class AuthorsFileParser implements AuthorsFileParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(AuthorsFile $file): AuthorList {
    $authors = new AuthorList();
    $json = \file_get_contents($file->getPathname());
    $json_data = Json::decode($json);
    $directory = \dirname($file->getPathname());
    foreach ($json_data as $id => $values) {
      // If values is not an array, just skip. It can be `$schema` value.
      if (!\is_array($values)) {
        continue;
      }
      if (isset($values['image'])) {
        $values['image'] = $directory . DIRECTORY_SEPARATOR . $values['image'];
      }
      $author = Author::createFromArray($id, $values);
      $authors->addAuthor($author);
    }
    return $authors;
  }

}
