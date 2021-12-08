<?php

declare(strict_types=1);

namespace Drupal\druki_author\Parser;

use Drupal\druki_author\Data\AuthorList;
use Drupal\druki_author\Data\AuthorsFile;

/**
 * Defines an interface for authors.json file parser.
 */
interface AuthorsFileParserInterface {

  /**
   * Parses file with authors.
   *
   * @param \Drupal\druki_author\Data\AuthorsFile $file
   *   The authors file.
   *
   * @return \Drupal\druki_author\Data\AuthorList
   *   A list with authors.
   */
  public function parse(AuthorsFile $file): AuthorList;

}
