<?php

namespace Drupal\druki_git\Git;

/**
 * Provide git helper interface.
 */
interface GitHelperInterface {

  /**
   * Pulls from remote repository.
   *
   * @param string $dir
   *   The absolute path to working dir.
   *
   * @return string
   *   The command output.
   */
  public static function pull(string $dir): string;

  /**
   * Gets last commit ID (hash).
   *
   * @param string $dir
   *   The absolute path to working dir.
   *
   * @return string
   *   The command output.
   */
  public static function getLastCommitId(string $dir): string;

  /**
   * Gets the file list commit ID.
   *
   * @param string $filepath
   *   The relative path to the file.
   * @param string $dir
   *   The absolute path to working dir.
   *
   * @return string
   *   The command output.
   */
  public static function getFileLastCommitId(string $filepath, string $dir): string;

  /**
   * Gets the file list commit ID.
   *
   * @param string $filepath
   *   The relative path to the file.
   * @param string $dir
   *   The absolute path to working dir.
   *
   * @return array
   *   The array with contribution statistics.
   *
   * @see https://stackoverflow.com/a/43042363/4751623
   */
  public static function getFileCommitsInfo(string $filepath, string $dir): array;

}
