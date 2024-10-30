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

namespace WorldlineOP\PrestaShop\Presenter;

if (!defined('_PS_VERSION_')) {
    exit;
}
use OnlinePayments\Sdk\Domain\WebhooksEvent;
use WorldlineOP\PrestaShop\Configuration\Entity\Settings;
use WorldlineOP\PrestaShop\Logger\LoggerFactory;

/**
 * Class TransactionPresenter
 */
class WebhookEventPresenter implements PresenterInterface
{
    public const EVENTS_PAYMENT_AUTHORIZED = [
        'payment.pending_approval',
        'payment.pending_completion',
        'payment.pending_capture',
    ];
    public const EVENTS_PAYMENT_ACCEPTED = ['payment.captured'];
    public const EVENTS_PAYMENT_PENDING = ['payment.authorization_requested'];
    public const EVENTS_REFUNDED = ['payment.refunded'];
    public const EVENTS_PAYMENT_CANCELLED = ['payment.cancelled'];
    public const EVENTS_PAYMENT_REJECTED = ['payment.rejected'];

    /** @var GetPaymentPresenter */
    private $paymentPresenter;

    /** @var GetRefundPresenter */
    private $refundPresenter;

    /** @var \Monolog\Logger */
    private $logger;

    /**
     * WebhookEventPresenter constructor.
     *
     * @param GetPaymentPresenter $paymentPresenter
     * @param GetRefundPresenter $refundPresenter
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(
        GetPaymentPresenter $paymentPresenter,
        GetRefundPresenter $refundPresenter,
        LoggerFactory $loggerFactory
    ) {
        $this->paymentPresenter = $paymentPresenter;
        $this->refundPresenter = $refundPresenter;
        $this->logger = $loggerFactory->setChannel('Webhooks');
    }

    /**
     * @param WebhooksEvent $event
     * @param Settings $settings
     */
    public function handlePending($event, $settings)
    {
        $paymentEvents = array_merge(
            self::EVENTS_PAYMENT_AUTHORIZED,
            self::EVENTS_PAYMENT_ACCEPTED,
            self::EVENTS_PAYMENT_REJECTED
        );

        if (in_array($event->getType(), $paymentEvents)) {
            $this->logger->debug('Sleeeeep', ['time' => $settings->advancedSettings->paymentSettings->safetyDelay]);
            sleep($settings->advancedSettings->paymentSettings->safetyDelay);
        }
    }

    /**
     * @param WebhooksEvent|bool $event
     * @param int|bool $idShop
     *
     * @return TransactionPresented
     *
     * @throws \PrestaShopException
     * @throws \PrestaShop\Decimal\Exception\DivisionByZeroException
     */
    public function present($event = false, $idShop = false)
    {
        $paymentEvents = array_merge(
            self::EVENTS_PAYMENT_AUTHORIZED,
            self::EVENTS_PAYMENT_ACCEPTED,
            self::EVENTS_PAYMENT_CANCELLED,
            self::EVENTS_PAYMENT_REJECTED
        );
        if (in_array($event->getType(), self::EVENTS_REFUNDED)) {
            $presentedData = $this->refundPresenter->present($event->getRefund(), $idShop);
        } elseif (in_array($event->getType(), $paymentEvents)) {
            $presentedData = $this->paymentPresenter->present($event->getPayment(), $idShop);
        } else {
            $presentedData = new TransactionPresented();
        }
        $this->logger->debug('Returning data', ['data' => $presentedData]);

        return $presentedData;
    }
}
