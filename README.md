<p align="center">
  <img src="https://i.imgur.com/GVA0m4I.png" alt="Druki" width="223">
</p>

# Druki Website

> Wikipedia is the best thing ever. Anyone in the world can write anything they want about any subject. So you know you are getting the best possible information.
> — <cite>[Michael Scott](https://www.youtube.com/watch?v=kFBDn5PiL00)</cite>

Welcome to the public home of Druki Website.

**Druki** — wiki-alike [website](druki.ru) about Drupal made on Drupal. This repository contains the source code of that website.

It was inspired by many other documentation sites, and their content editing process, but most important inspiration comes from ArchWiki. This project is trying to achieve the same things, but about Drupal and on Drupal, because, why not?

The main content of this website stored on GitHub too in [content repository](https://github.com/Druki-ru/content). The current repository is responsible for fetching, processing and rendering this content using Drupal.

## Local development

### Local environment

Add this line `$config['config_split.config_split.dev']['status'] = TRUE;` to **settings.php**.

This is required for correct configuration split process.

### Install a local copy

1. Clone this repository by `git clone https://github.com/Druki-ru/website.git`.
1. Run `composer install`
1. (optional) Run `yarn install`
1. Run Drupal installation as usual.
1. Select the preferred language.
1. Select "Use existing configuration" and continue.

    ![Profile](https://i.imgur.com/vsVKAHD.png)

1. Make a cup of coffee ☕️ and wait until the installation is finished.
1. Enjoy your copy.

### Code static analyse and Testing

#### PHP Unit

```php
composer run-script phpunit
```

#### PHPStan

```php
composer run-script phpstan
```

#### PHPCS

```php
composer run-script phpcs
```
