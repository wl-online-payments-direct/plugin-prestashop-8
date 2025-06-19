# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.6] - 2025-12-06
### Changed
- Fix potential issues with missing payment transaction form Worldline DB table due to the DB query caching or using read DB replica.

## [2.0.5] - 2025-22-05
### Added
- Add compatibility with PrestaShop 8.2

## [2.0.4] - 2025-14-04
### Changed
- Update plugin translations

## [2.0.3] - 2025-31-03
### Added
- Add 3DS exemption types to the plugin

## [2.0.2] - 2025-21-03
### Changed
- Fix issue with submit button not being deactivated when card fields are not added on checkout

## [2.0.1] - 2024-12-03
### Fixed

- Error after uninstall module 

## [2.0.0] - 2024-30-10
### Added

- Add compliance with PS 8
- Always return a 200 response to a webhook call

### Changed
- Update WebhookEventPresenter

### Fixed

- Fix amounts inside capture & refund blocks
- Fix ProductsType empty 
- Fix fraud display
- Fix amount verification
- Fix lock in processor
