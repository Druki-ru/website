<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides a base class for existing site testings.
 */
abstract class DrukiExistingSiteBase extends ExistingSiteBase {

  /**
   * {@inheritdoc}
   */
  public function drupalLogin(AccountInterface $account): void {
    if ($this->loggedInUser) {
      $this->drupalLogout();
    }

    $this->drupalGet(Url::fromRoute('user.login'));
    $form_input = [
      'name' => $account->getAccountName(),
      'pass' => $account->passRaw,
    ];
    $this->submitForm($form_input, new TranslatableMarkup('Log in'));

    $request = $this->container->get('request_stack')->getCurrentRequest();
    $session_configuration = $this->container->get('session_configuration');
    $session_options = $session_configuration->getOptions($request);
    $session_id = $this->getSession()->getCookie($session_options['name']);
    $account->sessionId = $session_id;

    $this->assertTrue($this->drupalUserIsLoggedIn($account));

    $this->loggedInUser = $account;
    $this->container->get('current_user')->setAccount($account);
  }

}
