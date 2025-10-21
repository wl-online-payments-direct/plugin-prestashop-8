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

namespace WorldlineOP\PrestaShop\Serializer;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class SettingsEncoder
 */
class SettingsEncoder extends JsonEncoder
{
    /**
     * @param mixed  $data
     * @param string $format
     * @param array  $context
     *
     * @return string
     */
    public function encode(mixed $data, $format, array $context = []): string
    {
        // Remove 'extra' key if it exists
        if (is_array($data) && array_key_exists('extra', $data)) {
            unset($data['extra']);
        }

        return parent::encode($data, $format, $context);
    }
}
