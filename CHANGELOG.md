# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.12] - 2025-13-08
### Changed
- Add null checking in liability/exemption chain

## [2.0.11] - 2025-11-08
### Changed
- Add missing translations

## [2.0.10] - 2025-24-07
### Changed
- Remove extension validation for template filename
- Implement omit order item details button and logic
- Fix order total webhook issue

## [2.0.9] - 2025-01-07
### Changed
- Implement new payment method : Mealvouchers
- Implement new payment method : Chèque-Vacances Connect (CVCO)

## [2.0.8] - 2025-19-06
### Changed
- Align changes and versions with production repository
- Update plugin translations

## [2.0.7] - 2025-18-06
### Fixed
- Error after uninstall module
- Mask Payment API & Webhook secrets

## [2.0.6] - 2025-17-06
### Fixed
- Fix issue when order is paid using GooglePay and fails to render in the backoffice

## [2.0.5] - 2025-22-05
### Changed
- Fix potential issues with missing payment transaction form
Worldline DB table due to the DB query caching or using read DB replica.

## [2.0.4] - 2025-22-05
### Added
- Add compatibility with PrestaShop 8.2

## [2.0.3] - 2025-14-04
### Changed
- Update plugin translations

## [2.0.2] - 2025-31-03
### Added
- Add 3DS exemption types to the plugin

## [2.0.1] - 2025-21-03
### Changed
- Fix issue with submit button not being deactivated when card fields are not added on checkout

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
