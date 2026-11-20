# Changelog

## 5.0.0 - TBD

### Changed

* Requires PHP 8.3, 8.4, 8.5 or 8.6.
* `PluginManager` is a PSR-11 container and no longer extends `Laminas\ServiceManager\AbstractPluginManager`.
* `ControllerEvent` extends `Laminas\EventManager\Event` directly.
* `dispatchEvent()` returns `ControllerEvent|ResponseInterface`.
* `getPluginManager()` returns `PluginManager` and `plugin()` returns `PluginInterface`.
* `UrlHelperPlugin` takes a `?string` fragment identifier.
* Invalid entries in `dot_controller.event_listeners` throw a `RuntimeException` with a clear message.
* Allows `mezzio/mezzio-template` 3.x.
* PHPStan level 8, PHPUnit 12.

### Added

* `PluginNotFoundException`, `InvalidPluginException` and `InvalidArgumentException`.
* `PluginManager::setAlias()` and the `aliases` key of the plugin manager configuration.
* Version 5 documentation with an upgrade guide.

### Deprecated

* Nothing

### Removed

* PHP 8.2 support.
* The requirements `laminas/laminas-servicemanager` and `dotkernel/dot-event`.
* Support for plugin manager configuration keys other than `factories` and `aliases`.

### Fixed

* A listener that returned a response caused a `TypeError` and could not stop the dispatch.
* `TemplatePlugin` with a `null` template name caused a `TypeError`.
* A `debug` configuration value that was not a boolean caused a `TypeError`.
* An `action` request attribute that was not a string caused a `TypeError`.
* A configuration without the `dot_controller` key caused a PHP warning.

## 4.2.0 - 2025-11-03

### Changed

* Removed PHP 8.1, added PHP 8.4 and 8.5 support (#51).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 4.1.0 - 2025-02-28

### Changed

* Replaced Psalm with PHPStan (#46).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 4.0.1 - 2025-01-27

### Changed

* Bumped Psalm to 6.x (#44).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 4.0.0 - 2025-01-15

### Changed

* Nothing

### Added

* Support for `laminas/laminas-servicemanager` 4.x (#42).

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.6.0 - 2025-11-03

### Changed

* Removed PHP 8.1, added PHP 8.4 and 8.5 support (#50).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.5.1 - 2024-05-03

### Changed

* Same code as 3.5.0.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.5.0 - 2024-04-25

### Changed

* Disabled the `laminas/laminas-dependency-plugin` Composer plugin (#39).

### Added

* Documentation for version 3 (#39).

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.4.3 - 2023-11-30

### Changed

* Nothing

### Added

* PHP 8.3 support (#37).

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.4.2 - 2023-08-28

### Changed

* Nothing

### Added

* Codecov (#34).

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.4.1 - 2023-08-18

### Changed

* Updated README.md (#29, #30).
* Issue #31 (#32).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.4.0 - 2023-08-07

### Changed

* Nothing

### Added

* Psalm, coding standard check, static analysis and tests (#27).

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.3.0 - 2023-03-29

### Changed

* Nothing

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* `laminas/laminas-dependency-plugin` (#25).

### Fixed

* Nothing

## 3.2.1 - 2022-04-29

### Changed

* Allow exactly PHP 8.0 and 8.1.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.2.0 - 2022-04-29

### Changed

* Allow PHP 8.0 and 8.1.
* Updated the tags in README.md.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.1.1 - 2020-11-02

### Changed

* Require version 2 of `laminas/laminas-dependency-plugin`.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.1.0 - 2020-11-02

### Changed

* Composer 2 compatibility.
* Update composer.json to require version 2 of laminas/laminas-dependency-plugin to be Composer 2 compatible.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.0.0 - 2020-05-08

### Changed

* Updated package dependencies and php version.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 2.0.0 - 2020-01-30

### Changed

* Laminas and Mezzio migration.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Support

### Fixed

* Nothing

## 1.0.0 - 2018-04-11

* [BC break] replaced delegate with handler

## 0.3.2 - unreleased

### Changed

* after dispatch event now considers the event parameter `response` to return if the event listeners did not return a ResponseInterface directly

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 0.3.1 - 2017-05-09

### Changed

* throw a more appropriate error message in case controller was not initialized properly

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 0.3.0 - 2017-04-21

### Changed

* added the controller as dispatch event parameter
* controller event names

### Added

* new controller event `ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH` after dispatching, which allows you to capture the returned response, and possible override the response generation

### Deprecated

* Nothing

### Removed

* `ControllerEvent::EVENT_CONTROLLER_DISPATCH` renamed to `ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH`

### Fixed

* Nothing

## 0.2.1 - 2017-03-18

### Changed

* updated expressive helpers dependency to 4.0

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 0.2.0 - 2017-03-15

### Changed

* Updated factories to use PSR11 container
* Updated base controller classes to use PSR15 middleware
* Updated `UrlHelperPlugin` to use new version of expressive helpers

### Added

* `UrlHelperPlugin` now supports the additional `UrlHelper` generate parameters
* Abstract controller added property `DelegateInterface` $delegate, replacing the $next

### Deprecated

* Nothing

### Removed

* $next property from AbstractController(use $delegate instead)
* $response property from AbstractController

### Fixed

* Nothing

## 0.1.1 - 2017-03-11

### Added

* Update php file headers doc blocks

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 0.1.0 - 2017-03-07

Initial tagged release

### Added

* Everything

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing
