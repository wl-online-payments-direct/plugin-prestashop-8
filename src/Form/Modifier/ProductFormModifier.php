<?php
/**
 * 2021 Worldline Online Payments
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * @author    PrestaShop partner
 * @copyright 2021 Worldline Online Payments
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License
 *   (AFL 3.0)
 */

declare(strict_types=1);

namespace WorldlineOP\PrestaShop\Form\Modifier;

use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\FormBuilderInterface;
use WorldlineOP\PrestaShop\Builder\HostedPaymentRequestBuilder;
use WorldlineOP\PrestaShop\Form\Type\GiftCardTabType;
use WorldlineOP\PrestaShop\Utils\Tools as ToolsWorldline;

class ProductFormModifier
{
    private $formBuilderModifier;

    public function __construct(
        FormBuilderModifier $formBuilderModifier
    )
    {
        $this->formBuilderModifier = $formBuilderModifier;
    }

    public function modify(?int $productId, FormBuilderInterface $productFormBuilder): void
    {
        $currentType = $productId
            ? ToolsWorldline::getGiftCardTypeByIdProduct($productId)
            : HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_NONE;

        $this->formBuilderModifier->addAfter(
            $productFormBuilder,
            'extra_modules',
            'worldlineop_gift_card',
            GiftCardTabType::class,
            [
                'data' => [
                    'gift_card_type' => $currentType,
                ],
            ]
        );
    }
}
