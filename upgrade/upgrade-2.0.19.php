<?php
/**
 * 2021 Worldline Online Payments
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    PrestaShop partner
 * @copyright 2021 Worldline Online Payments
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

const PAYMENT_METHODS_SETTINGS_COLUMN_NAME = 'WORLDLINEOP_PAYMENT_METHODS_SETTINGS';
const DEFAULT_GENERIC_LOGO_FILENAME = 'worldlineop_symbol.svg';
const DEFAULT_IFRAME_LOGO_FILENAME = 'cb_visa_mc_amex.svg';

/**
 * Updates module to the version 2.0.19
 * - If 'isDefaultGenericLogo' is missing => check if current logo is default and set flag accordingly
 * - If 'isDefaultIframeLogo' is missing => check if current logo is default and set flag accordingly
 * - Preserve existing values otherwise
 *
 * @param Worldlineop $module
 *
 * @return bool
 */
function upgrade_module_2_0_19($module)
{
    $logger = $module->getLogger()->withName('Upgrade_2_0_19');
    $logger->info('Upgrade to v2.0.19 started');

    Shop::setContext(Shop::CONTEXT_ALL);
    if (Configuration::hasContext(PAYMENT_METHODS_SETTINGS_COLUMN_NAME, null, Shop::getContext())) {
        try {
            update_payment_methods_settings_2_0_19();
            $logger->info('Updated payment methods settings for all shops');
        } catch (Exception $e) {
            $logger->error('Error updating all shops context: ' . $e->getMessage());
        }
    }

    $shops = Shop::getShops();
    foreach ($shops as $shop) {
        Shop::setContext(Shop::CONTEXT_SHOP, (int) $shop['id_shop']);
        if (Configuration::hasKey(PAYMENT_METHODS_SETTINGS_COLUMN_NAME, null, null, (int) $shop['id_shop'])) {
            try {
                update_payment_methods_settings_2_0_19();
                $logger->info('Updated payment methods settings for shop ID: ' . $shop['id_shop']);
            } catch (Exception $e) {
                $logger->error('Error updating shop ' . $shop['id_shop'] . ': ' . $e->getMessage());
            }
        }
    }

    $logger->info('Upgrade to v2.0.19 finished with success');

    return true;
}

/**
 * Update payment methods settings with default logo flags
 *
 * @return void
 *
 * @throws Exception
 */
function update_payment_methods_settings_2_0_19()
{
    $config = Configuration::get(PAYMENT_METHODS_SETTINGS_COLUMN_NAME);

    if (empty($config)) {
        return;
    }

    $settings = json_decode($config, true);

    if (!is_array($settings)) {
        throw new Exception('Invalid payment methods settings format');
    }

    $changed = false;

    if (!array_key_exists('isDefaultGenericLogo', $settings) || $settings['isDefaultGenericLogo'] === null) {
        $currentGenericLogo = isset($settings['genericLogoFilename']) ? $settings['genericLogoFilename'] : '';
        $settings['isDefaultGenericLogo'] = (empty($currentGenericLogo) || $currentGenericLogo === DEFAULT_GENERIC_LOGO_FILENAME);
        $changed = true;
    }

    if (!array_key_exists('isDefaultIframeLogo', $settings) || $settings['isDefaultIframeLogo'] === null) {
        $currentIframeLogo = isset($settings['iframeLogoFilename']) ? $settings['iframeLogoFilename'] : '';
        $settings['isDefaultIframeLogo'] = (empty($currentIframeLogo) || $currentIframeLogo === DEFAULT_IFRAME_LOGO_FILENAME);
        $changed = true;
    }

    if ($changed) {
        Configuration::updateValue(
            PAYMENT_METHODS_SETTINGS_COLUMN_NAME,
            json_encode($settings)
        );
    }
}
