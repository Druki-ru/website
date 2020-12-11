<?php

namespace Drupal\druki\Drupal;

use Drupal\update\UpdateFetcherInterface;

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
   * Gets last minor version info for Drupal core.
   *
   * @return array|null
   *   The version info.
   */
  public function getCoreLastMinorVersion(): ?array {
    $releases = $this->fetchProjectData('drupal');
    $data = $this->parseXml($releases);

    $last_stable_version = $this->getCoreLastStableVersion();
    $version_parts = \explode('.', $last_stable_version['version']);
    $minor_version_pieces = [
      $version_parts[0],
      $version_parts[1],
      0,
    ];

    $minor_version = \implode('.', $minor_version_pieces);

    return empty($data['releases'][$minor_version]) ? [] : $data['releases'][$minor_version];
  }

  /**
   * Fetch project data.
   *
   * @param string $project_name
   *   The Drupal project name.
   *
   * @return string
   *   The request result.
   */
  protected function fetchProjectData(string $project_name): string {
    return $this->updateFetcher->fetchProjectData(['name' => $project_name]);
  }

  /**
   * Parses the XML of the Drupal release history info files.
   *
   * @param string $raw_xml
   *   A raw XML string of available release data for a given project.
   *
   * @return array|null
   *   Array of parsed data about releases for a given project, or NULL if there
   *   was an error parsing the string.
   *
   * @see https://updates.drupal.org/release-history/drupal/current
   */
  protected function parseXml(string $raw_xml): ?array {
    try {
      $xml = new \SimpleXMLElement($raw_xml);
    }
    catch (\Exception $e) {
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

        $data['releases'][$version]['security_covered'] = FALSE;
        if (isset($release->security->attributes()->covered)) {
          $data['releases'][$version]['security_covered'] = TRUE;
        }
      }
    }

    return $data;
  }

  /**
   * Gets project last stable release.
   *
   * @return array|null
   *   The last stable release version info, NULL if something wrong happens or
   *   stable release is missing.
   */
  public function getCoreLastStableVersion(): ?array {
    $releases = $this->fetchProjectData('drupal');
    $data = $this->parseXml($releases);

    foreach ($data['releases'] as $release) {
      // If release is security covered, this is stable one. The latest is
      // always on top, so the first found is the last stable release.
      if ($release['security_covered']) {
        return $release;
      }
    }

    return NULL;
  }

}
