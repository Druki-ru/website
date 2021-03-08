<?php

namespace Drupal\druki_content\Sync\Redirect;

use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\Finder\Finder;

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
   * @return \Drupal\druki_content\Sync\Redirect\RedirectFileList
   *   The list with redirect files.
   */
  public function findAll(string $directory): RedirectFileList {
    $finder = new Finder();
    $finder->name('redirects.csv');
    // Look only at specific directory without hierarchy.
    $finder->depth(0);

    $active_languages = $this->languageManager->getLanguages();
    $active_langcodes = \array_keys($active_languages);

    $redirect_file_list = new RedirectFileList();
    foreach ($active_langcodes as $langcode) {
      // The file must be in the root of language source content.
      $look_at = "{$directory}/docs/{$langcode}";
      $finder->in($look_at);
      if (!$finder->hasResults()) {
        continue;
      }
      $iterator = $finder->getIterator();
      $iterator->rewind();
      /** @var \Symfony\Component\Finder\SplFileInfo $first_file */
      $first_file = $iterator->current();
      $redirect_file_list->addFile(new RedirectFile($first_file->getPathname(), $langcode));
    }
    return $redirect_file_list;
  }

}
