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
