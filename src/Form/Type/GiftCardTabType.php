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

declare(strict_types=1);

namespace WorldlineOP\PrestaShop\Form\Type;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WorldlineOP\PrestaShop\Builder\HostedPaymentRequestBuilder;

class GiftCardTabType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $currentType = $options['data']['gift_card_type']
            ?? HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_NONE;

        $builder->add('gift_card_type', ChoiceType::class, [
            'label' => $this->trans('Product type', 'Modules.Worldlineop.Admin'),
            'required' => false,
            'mapped' => false,
            'data' => $currentType,
            'choices' => [
                $this->trans('None', 'Modules.Worldlineop.Admin') => HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_NONE,
                $this->trans('Food & Drink', 'Modules.Worldlineop.Admin') => HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_FOOD_DRINK,
                $this->trans('Home & Garden', 'Modules.Worldlineop.Admin') => HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_HOME_GARDEN,
                $this->trans('Gift & Flowers', 'Modules.Worldlineop.Admin') => HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_GIFT_FLOWERS,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => $this->trans('Worldline', 'Modules.Worldlineop.Admin'),
            'form_theme' => '@Modules/worldlineop/views/templates/admin/form/gift_card_tab.html.twig',
            'mapped' => false,
        ]);
    }
}
