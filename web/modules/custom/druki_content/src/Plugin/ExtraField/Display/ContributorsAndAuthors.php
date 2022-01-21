<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;

/**
 * Provides an extra field to display contributors and authors.
 *
 * @ExtraFieldDisplay(
 *   id = "contributors_and_authors",
 *   label = @Translation("Contributors & Authors"),
 *   visible = TRUE,
 *   bundles = {
 *     "druki_content.documentation",
 *   },
 * )
 */
final class ContributorsAndAuthors extends ExtraFieldDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity): array {
    return ['#markup' => '@todo Migrate and improve ContributorFormatter.'];
  }

}
