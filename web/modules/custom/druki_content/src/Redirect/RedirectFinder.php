<?php

namespace Drupal\druki_content\Redirect;

use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Provides finder for redirect files.
 */
final class RedirectFinder {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * RedirectFinder constructor.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;
  }

  /**
   * Search for redirect files.
   *
   * @param string $directory
   *   The path where to look at.
   *
   * @return \Symfony\Component\Finder\SplFileInfo
   *   The file info.
   *
   * @todo return list.
   */
  public function findAll(string $directory): SplFileInfo {
    $finder = new Finder();
    $finder->name('redirects.csv');
    // Look only at specific directory without hierarchy.
    $finder->depth(0);

    $active_languages = $this->languageManager->getLanguages();
    $active_langcodes = \array_keys($active_languages);

    foreach ($active_langcodes as $langcode) {
      // The file must be in the root of language source content.
      $look_at = "{$directory}/docs/{$langcode}";
      $finder->in($look_at);
      if (!$finder->hasResults()) {
        continue;
      }
      $iterator = $finder->getIterator();
      $iterator->rewind();
      $first_file = $iterator->current();
      return $first_file;
    }
  }

}
