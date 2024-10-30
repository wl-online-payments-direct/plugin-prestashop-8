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

namespace WorldlineOP\PrestaShop\Configuration\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Class PaymentMethod
 */
class PaymentMethod
{
    public const PM_GROUP_CARDS = 'Cards';
    public const PM_TYPE_CARD = 'card';

    /** @var bool */
    public $enabled;

    /** @var int */
    public $productId;

    /** @var string */
    public $identifier;

    /** @var string */
    public $type;

    /** @var string */
    public $logo;
}
