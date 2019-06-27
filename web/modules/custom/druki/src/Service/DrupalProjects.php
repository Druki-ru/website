<?php

namespace Drupal\druki\Service;

use Drupal\update\UpdateFetcherInterface;
use Exception;

/**
 * Fetches information about drupal projects.
 */
class DrupalProjects {

  /**
   * The update fetcher.
   *
   * @var \Drupal\update\UpdateFetcherInterface
   */
  protected $updateFetcher;

  /**
   * Constructs a new DrupalProjects object.
   *
   * @param \Drupal\update\UpdateFetcherInterface $update_fetcher
   *   The update fetcher.
   */
  public function __construct(UpdateFetcherInterface $update_fetcher) {
    $this->updateFetcher = $update_fetcher;
  }

  /**
   * Gets project last stable release.
   *
   * @param string $project_name
   *   The project name.
   *
   * @return string|null
   *   The last stable release version, NULL if something wrong happens or
   *   stable release is missing.
   */
  public function getProjectLastStableRelease(string $project_name): ?string {
    $releases = $this->updateFetcher->fetchProjectData(['name' => $project_name]);
    $data = $this->parseXml($releases);

    foreach ($data['releases'] as $release) {
      // Beta, alpha, rc and other not stable version will have additional value
      // under key "version_extra".
      if (!isset($release['version_extra'])) {
        return $release['version'];
      }
    }

    return NULL;
  }

  /**
   * Parses the XML of the Drupal release history info files.
   *
   * @param string $raw_xml
   *   A raw XML string of available release data for a given project.
   *
   * @return array
   *   Array of parsed data about releases for a given project, or NULL if there
   *   was an error parsing the string.
   */
  protected function parseXml($raw_xml) {
    try {
      $xml = new \SimpleXMLElement($raw_xml);
    }
    catch (Exception $e) {
      // SimpleXMLElement::__construct produces an E_WARNING error message for
      // each error found in the XML data and throws an exception if errors
      // were detected. Catch any exception and return failure (NULL).
      return NULL;
    }
    // If there is no valid project data, the XML is invalid, so return failure.
    if (!isset($xml->short_name)) {
      return NULL;
    }
    $data = [];
    foreach ($xml as $k => $v) {
      $data[$k] = (string) $v;
    }
    $data['releases'] = [];
    if (isset($xml->releases)) {
      foreach ($xml->releases->children() as $release) {
        $version = (string) $release->version;
        $data['releases'][$version] = [];
        foreach ($release->children() as $k => $v) {
          $data['releases'][$version][$k] = (string) $v;
        }
        $data['releases'][$version]['terms'] = [];
        if ($release->terms) {
          foreach ($release->terms->children() as $term) {
            if (!isset($data['releases'][$version]['terms'][(string) $term->name])) {
              $data['releases'][$version]['terms'][(string) $term->name] = [];
            }
            $data['releases'][$version]['terms'][(string) $term->name][] = (string) $term->value;
          }
        }
      }
    }

    return $data;
  }

}
