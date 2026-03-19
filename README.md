# MyAdmin Hotjar Analytics Plugin

A plugin for the [MyAdmin](https://github.com/detain/myadmin) control panel that integrates [Hotjar](https://www.hotjar.com/) analytics and heatmap tracking. This package provides event-driven hooks for embedding Hotjar scripts, managing analytics settings, and registering plugin requirements within the MyAdmin ecosystem.

## Badges

[![Build Status](https://github.com/detain/myadmin-hotjar-analytics/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-hotjar-analytics/actions)
[![Code Climate](https://codeclimate.com/github/detain/myadmin-hotjar-analytics/badges/gpa.svg)](https://codeclimate.com/github/detain/myadmin-hotjar-analytics)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/myadmin-plugins/hotjar-analytics/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/myadmin-plugins/hotjar-analytics/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-hotjar-analytics/version)](https://packagist.org/packages/detain/myadmin-hotjar-analytics)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-hotjar-analytics/downloads)](https://packagist.org/packages/detain/myadmin-hotjar-analytics)
[![License](https://poser.pugx.org/detain/myadmin-hotjar-analytics/license)](https://packagist.org/packages/detain/myadmin-hotjar-analytics)

## Installation

Install via Composer:

```sh
composer require detain/myadmin-hotjar-analytics
```

## Usage

The plugin registers itself through the MyAdmin plugin system using Symfony EventDispatcher hooks. It provides:

- **getHooks()** - Returns an array of event hooks the plugin subscribes to.
- **getMenu()** - Adds admin menu entries when the user has appropriate ACL permissions.
- **getRequirements()** - Registers class and function autoloading requirements with the plugin loader.
- **getSettings()** - Handles plugin-specific configuration settings.

## Requirements

- PHP >= 5.0
- ext-soap
- symfony/event-dispatcher ^5.0

## License

The MyAdmin Hotjar Analytics Plugin is licensed under the [LGPL-2.1](LICENSE) license.
