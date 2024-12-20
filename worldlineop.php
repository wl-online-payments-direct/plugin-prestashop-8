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
require_once 'vendor/autoload.php';

use Monolog\Logger;
use PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer;
use WorldlineOP\PrestaShop\Utils\Tools as ToolsWorldline;


/**
 * Class Worldlineop
 */
class Worldlineop extends PaymentModule
{
    /** @var string */
    public $theme;

    /** @var ServiceContainer */
    private $serviceContainer;

    /** @var Logger */
    public $logger;

    /**
     * Worldlineop constructor.
     */
    public function __construct()
    {
        $this->name = 'worldlineop';
        $this->author = 'Worldline Online Payments';
        $this->version = '2.0.1';
        $this->tab = 'payments_gateways';
        $this->module_key = '089d13d0218de8085259e542483f4438';
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        parent::__construct();
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '8', 'max' => '8.1.99'];
        //@formatter:off
        $this->displayName = $this->l('Worldline Online Payments');
        $this->description = $this->l('This module offers a 1-click integration to start accepting payments and grow your revenues by offering your customers with global and regional payment methods to sell across Europe.');
        //@formatter:on
        $this->theme = Tools::version_compare(_PS_VERSION_, '1.7.7', '>=') ? 'new-theme' : 'legacy';
    }

    /**
     * @return bool
     */
    public function install()
    {
        /** @var WorldlineOP\PrestaShop\Installer\Installer $installer */
        $installer = $this->getService('worldlineop.installer');
        if (false === parent::install()) {
            $installer->getLogger()->error('parent::install() returns false');

            return false;
        }
        try {
            $installer->run();
        } catch (\WorldlineOP\PrestaShop\Exception\ExceptionList $list) {
            foreach ($list as $item) {
                /** @var \WorldlineOP\PrestaShop\Exception\ExceptionList $e */
                $e = $item;
                $installer->getLogger()->error(sprintf('%s - File: %s - Line: %s - Trace: %s', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()));

                return false;
            }
        } catch (Exception $e) {
            $installer->getLogger()->error(sprintf('%s - File: %s - Line: %s - Trace: %s', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()));
            $this->_errors[] = $this->l('Worldline module could not be installed. Please check the logs inside the module "logs" directory.');

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (parent::uninstall()) {
            Configuration::deleteByName('WORLDLINEOP_ACCOUNT_SETTINGS');
            Configuration::deleteByName('WORLDLINEOP_ADVANCED_SETTINGS');
            Configuration::deleteByName('WORLDLINEOP_PAYMENT_METHODS_SETTINGS');
            ToolsWorldline::removeSymfonyCache();
            return true;
        }

        return false;
    }

    public function disable($force_all = false)
    {
        if (parent::disable($force_all)) {
            ToolsWorldline::removeSymfonyCache();
            return true;
        }
        return false;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        static $logger;

        if (null === $logger) {
            /** @var Logger $logger */
            $logger = $this->getService('worldlineop.logger');
        }

        return $logger;
    }

    /**
     * @param string $serviceName
     *
     * @return mixed
     */
    public function getService($serviceName)
    {
        if ($this->serviceContainer === null) {
            $this->serviceContainer = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
                $this->name . str_replace('.', '', $this->version),
                $this->getLocalPath()
            );
        }

        return $this->serviceContainer->getService($serviceName);
    }

    /**
     * @throws PrestaShopException
     */
    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminWorldlineopConfiguration'));
    }

    public function hookActionFrontControllerSetMedia()
    {
        $controller = Tools::getValue('controller');

        switch ($controller) {
            case 'order':
                $this->context->controller->registerJavascript(
                    'worldineoc-js-sdk',
                    'https://payment.preprod.direct.ingenico.com/hostedtokenization/js/client/tokenizer.min.js',
                    ['server' => 'remote', 'priority' => 1, 'position' => 'head', 'attribute' => 'defer']
                );
                $this->context->controller->registerStylesheet(
                    'worldlineop-css-paymentOptions',
                    $this->getPathUri() . 'views/css/front.css?version=' . $this->version,
                    ['server' => 'remote']
                );
                $this->context->controller->registerJavascript(
                    'worldlineop-js-paymentOptions',
                    $this->getPathUri() . 'views/js/paymentOptions.js?version=' . $this->version,
                    ['position' => 'head', 'priority' => 1000, 'server' => 'remote']
                );
                break;
            case 'redirect':
                $this->context->controller->registerJavascript(
                    'worldlineop-redirect-javascript',
                    $this->getPathUri() . 'views/js/redirect.js',
                    ['position' => 'bottom', 'priority' => 1000, 'server' => 'remote']
                );
                break;
        }
    }

    /**
     * @throws PrestaShopException
     */
    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == 'AdminOrders') {
            Media::addJsDef([
                'worldlineopAjaxTransactionUrl' => $this->context->link->getAdminLink(
                    'AdminWorldlineopAjaxTransaction',
                    true,
                    [],
                    ['ajax' => 1, 'token' => Tools::getAdminTokenLite('AdminWorldlineopAjaxTransaction')]
                ),
                'worldlineopGenericErrorMessage' => $this->l('An error occurred while processing your request. Please try again.'),
                'alertRefund' => $this->l('Do you confirm the refund of the funds?'),
                'alertCapture' => $this->l('Do you confirm the capture of the transaction?'),
                'alertCancel' => $this->l('Do you confirm the cancellation of the transaction?'),
            ]);
        }
    }

    /**
     * @return string
     */
    public function hookDisplayBackOfficeFooter()
    {
        if (Tools::getValue('controller') == 'AdminWorldlineopConfiguration') {
            return '
                <script type="text/javascript" src="' . $this->getPathUri() . 'views/js/config.js"></script>
                <script type="text/javascript" src="' . $this->getPathUri() . 'views/js/jquery.custom-file-input.js"></script>
            ';
        }

        return '';
    }

    /**
     * @return array
     */
    public function hookPaymentOptions()
    {
        try {
            /** @var \WorldlineOP\PrestaShop\Presenter\PaymentOptionsPresenter $paymentOptionsPresenter */
            $paymentOptionsPresenter = $this->getService('worldlineop.payment.presenter');
        } catch (Exception $e) {
            $this->logger->error('Error while presenting payment options', ['message' => $e->getMessage()]);

            return [];
        }

        return $paymentOptionsPresenter->present();
    }

    /**
     * @return string
     */
    public function hookDisplayPaymentByBinaries()
    {
        return $this->context->smarty->fetch($this->getLocalPath() . '/views/templates/front/hookDisplayPaymentByBinaries.tpl');
    }

    /**
     * @param $params
     *
     * @return string
     */
    public function hookDisplayPaymentTop($params)
    {
        if (Tools::getValue('worldlineopDisplayPaymentTopMessage')) {
            return $this->context->smarty->fetch($this->getLocalPath() . '/views/templates/front/hookDisplayPaymentTop.tpl');
        }

        return '';
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookCustomerAccount($params)
    {
        return $this->display(dirname(__FILE__), 'views/templates/front/hookCustomerAccount.tpl');
    }

    /**
     * @param int $idOrder
     *
     * @return string
     *
     * @throws Exception
     */
    public function hookAdminOrderCommon($idOrder)
    {
        $order = new Order((int) $idOrder);
        if (!Validate::isLoadedObject($order)/* || $order->module !== $this->name*/) {
            throw new Exception('Cannot load order');
        }

        if ($order->id_shop != $this->context->shop->id || Shop::getContext() !== Shop::CONTEXT_SHOP) {
            return $this->displayError(sprintf($this->l('Please change shop context to shop ID %d'), $order->id_shop));
        }
        try {
            /** @var \WorldlineOP\PrestaShop\Presenter\TransactionPresenter $transactionPresenter */
            $transactionPresenter = $this->getService('worldlineop.transaction.presenter');
            /** @var \WorldlineOP\PrestaShop\Presenter\ModuleConfigurationPresenter $settingsPresenter */
            $settingsPresenter = $this->getService('worldlineop.settings.presenter');

            $this->context->smarty->assign([
                'transactionData' => $transactionPresenter->present($idOrder),
                'settingsData' => $settingsPresenter->present(),
            ]);
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }

        return $this->display(dirname(__FILE__), 'views/templates/admin/hookAdminOrder_' . $this->theme . '.tpl');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAdminOrderMainBottom($params)
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.7', '<')) {
            return '';
        }

        try {
            $html = $this->hookAdminOrderCommon(Tools::getValue('id_order'));
        } catch (Exception $e) {
            return '';
        }
        $this->context->smarty->assign([
            'html' => $html,
        ]);

        return $this->display(dirname(__FILE__), 'views/templates/admin/hookAdminOrder_container.tpl');
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAdminOrderLeft($params)
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            return '';
        }

        try {
            $html = $this->hookAdminOrderCommon(Tools::getValue('id_order'));
        } catch (Exception $e) {
            return '';
        }
        $this->context->smarty->assign([
            'html' => $html,
        ]);

        return $this->display(dirname(__FILE__), 'views/templates/admin/hookAdminOrder_container.tpl');
    }

    /**
     * @param array $params
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function hookDisplayPDFInvoice($params)
    {
        /** @var OrderInvoice $invoice */
        $invoice = $params['object'];
        $order = new Order((int) $invoice->id_order);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }
        /** @var \WorldlineOP\PrestaShop\Repository\TransactionRepository $transactionRepository */
        $transactionRepository = $this->getService('worldlineop.repository.transaction');
        /** @var WorldlineopTransaction $transaction */
        $transaction = $transactionRepository->findByIdOrder($order->id);
        if (false === $transaction) {
            return '';
        }
        $transactionId = strstr($transaction->reference, '_', true);
        if (false === $transactionId) {
            $transactionId = $transaction->reference;
        }
        $this->context->smarty->assign([
            'worldlineop_transaction_id' => $transactionId,
        ]);

        return $this->display(dirname(__FILE__), 'views/templates/admin/hookDisplayPDFInvoice.tpl');
    }

    /**
     * @param mixed[] $params
     *
     * @return void
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $idProduct = (int) $params['id_product'];
        $this->context->smarty->assign([
            'worldlineopGCTypeNone' => \WorldlineOP\PrestaShop\Builder\HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_NONE,
            'worldlineopGCTypeFoodDrink' => \WorldlineOP\PrestaShop\Builder\HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_FOOD_DRINK,
            'worldlineopGCTypeHomeGarden' => \WorldlineOP\PrestaShop\Builder\HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_HOME_GARDEN,
            'worldlineopGCTypeGiftFlowers' => \WorldlineOP\PrestaShop\Builder\HostedPaymentRequestBuilder::GIFT_CARD_PRODUCT_TYPE_GIFT_FLOWERS,
            'worldlineopGCSelectedType' => Tools::getGiftCardTypeByIdProduct($idProduct),
        ]);

        return $this->display(dirname(__FILE__), 'views/templates/admin/hookDisplayAdminProductsExtra.tpl');
    }

    /**
     * @param mixed[] $params
     *
     * @return void
     */
    public function hookActionProductUpdate($params)
    {
        if ($form = Tools::getValue('worldlineopGiftCard')) {
            $idProduct = $params['id_product'];
            try {
                Db::getInstance()->insert('worldlineop_product_gift_card', ['id_product' => (int) $idProduct, 'product_type' => pSQL($form['type'])], false, true, Db::ON_DUPLICATE_KEY);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }
    }
}
