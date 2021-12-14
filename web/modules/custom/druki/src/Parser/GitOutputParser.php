<?php

declare(strict_types=1);

namespace Drupal\druki\Parser;

use Drupal\druki\Data\Contributor;
use Drupal\druki\Data\ContributorList;

/**
 * Provides parser for 'git shortlog' contributors information.
 */
final class GitOutputParser {

  /**
   * Parse contributors output from 'log' or 'shortlog'.
   *
   * @param string $log
   *   The CMD output.
   *
   * @return \Drupal\druki\Data\ContributorList
   *   A list with contributors.
   */
  public static function parseContributorsLog(string $log): ContributorList {
    $contributors = new ContributorList();
    $lines = \explode(\PHP_EOL, $log);
    foreach ($lines as $line) {
      \preg_match('/\s*[0-9]\s*(.*)\s<(.*)>/m', $line, $matches);
      if (\count($matches) != 3) {
        continue;
      }
      $contributor = new Contributor($matches[1], $matches[2]);
      $contributors->addContributor($contributor);
    }
    return $contributors;
  }

}
