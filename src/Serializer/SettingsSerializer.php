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
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class SettingsSerializer
 */
class SettingsSerializer
{
    /** @var Serializer */
    private $serializer;

    /** @var array */
    private $normalizationContext;

    /**
     * SettingsSerializer constructor.
     */
    public function __construct()
    {
        // Define normalization callbacks
        $this->normalizationContext = [
            ObjectNormalizer::CALLBACKS => [
                'redirectPaymentMethods' => fn($value) => $value ?? [],
                'iframePaymentMethods' => fn($value) => $value ?? [],
            ],
        ];

        // Base ObjectNormalizer
        $objectNormalizer = new ObjectNormalizer(null, null, null, new PhpDocExtractor());

        // Your custom denormalizers
        $advancedSettingsDenormalizer = new AdvancedSettingsDenormalizer();
        $advancedSettingsDenormalizer->setDenormalizer($objectNormalizer);

        $paymentMethodsSettingsDenormalizer = new PaymentMethodsSettingsDenormalizer();
        $paymentMethodsSettingsDenormalizer->setDenormalizer($objectNormalizer);

        // Custom encoder
        $settingsEncoder = new SettingsEncoder();

        // Build serializer
        $this->serializer = new Serializer(
            [
                $advancedSettingsDenormalizer,
                $paymentMethodsSettingsDenormalizer,
                $objectNormalizer,
                new ArrayDenormalizer()
            ],
            [$settingsEncoder]
        );
    }

    /**
     * Returns the serializer instance.
     *
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    /**
     * Returns the normalization context including callbacks.
     *
     * @return array
     */
    public function getNormalizationContext(): array
    {
        return $this->normalizationContext;
    }
}
