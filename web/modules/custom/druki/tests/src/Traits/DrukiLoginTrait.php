<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Traits;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides a trait for proper login form submission.
 *
 * @see https://www.drupal.org/project/drupal/issues/3222549
 */
trait DrukiLoginTrait {

  /**
   * {@inheritdoc}
   */
  public function drupalLogin(AccountInterface $account): void {
    if ($this->loggedInUser) {
      $this->drupalLogout();
    }

    $this->drupalGet(Url::fromRoute('user.login'));
    $this->submitForm([
      'name' => $account->getAccountName(),
      'pass' => $account->passRaw,
    ], new TranslatableMarkup('Log in'));

    // @see ::drupalUserIsLoggedIn()
    $account->sessionId = $this->getSession()
      ->getCookie(\Drupal::service('session_configuration')
        ->getOptions(\Drupal::request())['name']);
    $this->assertTrue($this->drupalUserIsLoggedIn($account), (string) new FormattableMarkup('User %name successfully logged in.', ['%name' => $account->getAccountName()]));

    $this->loggedInUser = $account;
    $this->container->get('current_user')->setAccount($account);
  }

}
