<p align="center">
  <img src="https://i.imgur.com/GVA0m4I.png" alt="Druki" width="223">
</p>

<p align="center">
  <img src="https://github.com/Druki-ru/website/workflows/Continuous%20Integration/badge.svg?branch=9.x" alt="Continuous Integration">
  <img src="https://github.com/Druki-ru/website/workflows/Tests/badge.svg" alt="Tests">
  <img src="https://img.shields.io/endpoint?url=https%3A%2F%2Fdruki.ru%2Fshields%2Fdrupal-core" alt="Website Drupal Core version">
</p>

# Druki Website

> Wikipedia is the best thing ever. Anyone in the world can write anything they want about any subject. So you know you are getting the best possible information.
> — <cite>[Michael Scott](https://www.youtube.com/watch?v=kFBDn5PiL00)</cite>

Welcome to the public home of Druki Website.

**Druki** — wiki-alike [website](https://druki.ru) about Drupal made on Drupal. This repository contains the source code of that website.

It was inspired by many other documentation sites, and their content editing process, but most important inspiration comes from ArchWiki. This project is trying to achieve the same things, but about Drupal and on Drupal, because, why not?

The main content of this website stored on GitHub too in [content repository](https://github.com/Druki-ru/content). The current repository is responsible for fetching, processing and rendering this content using Drupal.

## Local development

### Local environment

Add this line `$config['config_split.config_split.dev']['status'] = TRUE;` to **settings.php**.

This is required for correct configuration split process.

If the content synchronization not working properly, set the path of your local Git binary to **settings.php** (e.g. `$settings['druki_git_binary'] = '/usr/local/bin/git;'`).

### Install a local copy

**Warning!** Currently, Drupal core has a bug [#3176625](https://www.drupal.org/project/drupal/issues/3176625). Before it resolved, you must patch core before running installation process, otherwise it will fail during config imports. Nothing can be done from our site. _The current versions of codebase include that patch._

1. Clone this repository by `git clone https://github.com/Druki-ru/website.git`.
1. Run `composer install`.
1. Copy file `/web/sites/default/default.settings.php`, rename it to **settings.php** and place in the same path.
1. Add `$settings['config_sync_directory'] = '../config/sync';` to your **settings.php**.
1. Run `yarn install && yarn run compile` to build CSS and JavaScript files.
1. Open your local website address.
1. The installation will start automatically and prepare website for you!
1. Make a cup of coffee ☕️ and wait until the installation is finished.
1. Enjoy your copy.

## Compile CSS and JS

If you want to modify sites theme or some of the JSs, you must run compilation process to do so.

- `yarn install` (`npm install`) (if not yet done)
- `yarn run compile` (`npm run compile`) - to one time build.
- `yarn run watch` (`npm run watch`) - to watch for file changes and compile them.

These scripts will:

- Compile PostCSS to CSS files.
- Compile JavaScript files with `.es6.js` ending into `.js` files, compress and optimize them.


The dist files are placed at the same folder where is a source. It can be a bit overwhelming in project tree, so it's suggested for JetBrains IDEs users to do:

1. `SHIFT` + `SHIFT`
1. Type `File nesting`, select found element.
1. In opened window add new rules:
  - `.es6.js` | `.js`
  - `.pcss` | `.css`

As a result, it will make your structure clean and usable.

![File nesting PHPStorm](https://i.imgur.com/iIDcfTD.png)

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

### Drush Commands

### druki-content:sync-file

The command `druki-content:sync-file` allows you to run import/update process for the specific file. Expects filepath to the file which should be processed relative to content source path.

**Options:**

* `locale`: The langcode which used for content. By default, uses sites default.

Examples:

```bash
drush druki-content:sync-file docs/ru/drupal/index.md
drush druki-content:sync-file docs/ru/drupal/index.md --locale=en
```
