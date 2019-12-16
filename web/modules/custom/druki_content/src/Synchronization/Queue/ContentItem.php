<?php

namespace Drupal\druki_content\Synchronization\Queue;

/**
 * Provides data-object represents queue single item to process.
 */
final class ContentItem {

  /**
   * The langcode.
   *
   * @var string
   */
  private $langcode;

  /**
   * The path (uri) to source.
   *
   * @var string
   */
  private $path;

  /**
   * The filename.
   *
   * @var string
   */
  private $filename;

  /**
   * The relative to git repository root pathname.
   *
   * @var string
   */
  private $relativePathname;

  /**
   * The last commit ID.
   *
   * @var string
   */
  private $lastCommitId;

  /**
   * An array with contribution statistics.
   *
   * @var array[]
   */
  private $contributionStatistics;

  /**
   * ContentQueueItem constructor.
   *
   * @param string $langcode
   *   The langcode.
   * @param string $path
   *   The path.
   * @param string $relative_pathname
   *   The relative pathname.
   * @param string $filename
   *   The filename.
   * @param string $last_commit_id
   *   The last commit id.
   * @param array $contribution_statistics
   *   The array with contribution statistics.
   */
  public function __construct(
    string $langcode,
    string $path,
    string $relative_pathname,
    string $filename,
    string $last_commit_id,
    array $contribution_statistics
  ) {

    $this->langcode = $langcode;
    $this->path = $path;
    $this->relativePathname = $relative_pathname;
    $this->filename = $filename;
    $this->lastCommitId = $last_commit_id;
    $this->contributionStatistics = $contribution_statistics;
  }

  /**
   * Gets langcode.
   *
   * E.g.: "ru".
   *
   * @return string
   *   The langcode.
   */
  public function getLangcode(): string {
    return $this->langcode;
  }

  /**
   * Gets path to file.
   *
   * E.g.: "public://druki-content-source/docs/ru/code-of-conduct.md".
   *
   * @return string
   *   The uri value.
   */
  public function getPath(): string {
    return $this->path;
  }

  /**
   * Gets filename.
   *
   * E.g.: "code-of-conduct.md".
   *
   * @return string
   *   The filename.
   */
  public function getFilename(): string {
    return $this->filename;
  }

  /**
   * Gets last commit ID.
   *
   * E.g.: "3b9bb1dae420e688c9e23a4879760395cf3bd490".
   *
   * @return string
   *   The commit hash.
   */
  public function getLastCommitId(): string {
    return $this->lastCommitId;
  }

  /**
   * Gets contribution stats.
   *
   * E.g.:
   *
   * @code
   * [
   *   0 => [
   *     'count' => '5',
   *     'name' => 'Niklan',
   *     'email' => 'niklanrus@gmail.com',
   *   ],
   * ];
   * @endcode
   *
   * @return array[]
   *   The array with contribution stats.
   */
  public function getContributionStatistics(): array {
    return $this->contributionStatistics;
  }

  /**
   * Gets relative pathname.
   *
   * E.g.: "docs/ru/code-of-conduct.md".
   *
   * @return string
   *   The relative pathname.
   */
  public function getRelativePathname(): string {
    return $this->relativePathname;
  }

}
