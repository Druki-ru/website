<?php

declare(strict_types=1);

namespace Drupal\druki\Twig;

use Drupal\druki\Utility\Anchor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Provides custom Twig Extensions.
 */
final class DrukiTwigExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array {
    return [
      new TwigFunction('druki_anchor', [Anchor::class, 'generate']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters(): array {
    return [
      new TwigFilter('druki_anchor', [Anchor::class, 'generate']),
    ];
  }

}
