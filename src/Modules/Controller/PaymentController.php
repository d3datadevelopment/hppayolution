<?php

namespace D3\Hppayolution\Modules\Controller;

use D3\Heidelpay\Models\Factory;
use D3\Heidelpay\Models\Payment\Exception\PaymentNotReferencedToHeidelpayException;
use D3\Heidelpay\Models\Payment\Payment;
use D3\Heidelpay\Models\Settings\Heidelpay;
use D3\Heidelpay\Models\Viewconfig;
use D3\Hppayolution\Models\Payment\Payolution;
use D3\Hppayolution\Models\Request\Payolution as RequestPayolution;
use D3\Hppayolution\Models\Verify\Phone;
use D3\ModCfg\Application\Model\Configuration\d3_cfg_mod;
use D3\ModCfg\Application\Model\Log\d3log;
use OxidEsales\Eshop\Application\Model\Basket as OxidBasket;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\Payment as OxidPayment;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

/**
 */
class PaymentController extends PaymentController_parent
{

    /**
     * Injects the Trusted Shops Excellence protection into the POST superglobal
     *
     * @return mixed
     * @throws StandardException
     * @throws \D3\ModCfg\Application\Model\Exception\d3ShopCompatibilityAdapterException
     * @throws \D3\ModCfg\Application\Model\Exception\d3_cfg_mod_exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function validatePayment()
    {
        $return = parent::validatePayment();

        if (empty($return) || false === stristr($return, 'order')) {
            return $return;
        }

        $paymentId = $this->getD3PaymentId();
        if (false == $paymentId) {
            return false;
        }

        $heidelPayment = $this->d3GetHeidelPayment($paymentId);

        if ($heidelPayment instanceof Payolution) {
            return $this->handleD3HeidelpayPayolution($paymentId);
        }

        return $return;
    }



    /**
     * @param OxidPayment $oxidPayment
     * @param string      $sTemplate
     *
     * @return string
     * @throws PaymentNotReferencedToHeidelpayException
     * @throws StandardException
     * @throws \D3\Heidelpay\Models\Settings\Exception\EmptyPaymentlistException
     * @throws \D3\ModCfg\Application\Model\Exception\d3ShopCompatibilityAdapterException
     * @throws \D3\ModCfg\Application\Model\Exception\d3_cfg_mod_exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function d3GetPaymentFormTemplateName(OxidPayment $oxidPayment, $sTemplate = '')
    {
        if (empty($sTemplate)) {
            $sTemplate = d3_cfg_mod::get('d3heidelpay')->getMappedThemeId();
        }

        $sTemplate = strtolower($sTemplate);

        /** @var Viewconfig $oHeidelpayViewConfig */
        $oHeidelpayViewConfig = oxNew(
            Viewconfig::class,
            d3_cfg_mod::get('d3heidelpay'),
            Registry::get(Registry::class),
            oxNew(Factory::class, d3_cfg_mod::get('d3heidelpay'))
        );
        $oHeidelPaySettings   = $oHeidelpayViewConfig->getSettings();
        $return               = $this->d3GetDefaultPaymentFormTemplateName($oxidPayment);
        if ($oHeidelPaySettings->isAssignedToHeidelPayment($oxidPayment)) {
            $oHeidelPayment = $oHeidelPaySettings->getPayment($oxidPayment);
            if ($oHeidelPayment instanceof Payolution) {
                /** @var  Payolution $oHeidelPayment */
                $return = $oHeidelPayment->getDefaultTemplateName($sTemplate);
            }
        }

        return $return;
    }

    /**
     * @return string
     * @throws PaymentNotReferencedToHeidelpayException
     * @throws StandardException
     * @throws \D3\Heidelpay\Models\Settings\Exception\EmptyPaymentlistException
     * @throws \D3\ModCfg\Application\Model\Exception\d3ShopCompatibilityAdapterException
     * @throws \D3\ModCfg\Application\Model\Exception\d3_cfg_mod_exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function render()
    {
        $mReturn = parent::render();

        $this->addTplParam('showD3PayolutionError', false);
        $this->addTplParam('blD3HeidelpayPayolutionRequirePhone', false);
        $this->addTplParam(
            'blD3HeidelpayAllowPayolution',
            $this->isHeidelpayPayolutionAllowed($this->getSession()->getBasket())
        );

        $heidelPayment = $this->d3GetHeidelPayment($this->getD3PaymentId());
        if ($heidelPayment instanceof Payolution) {
            $payolutionerror = Registry::getSession()->getVariable('payolutionerror');
            if ($payolutionerror) {
                Registry::getSession()->deleteVariable('payolutionerror');

                $translation = Registry::getLang()->translateString($payolutionerror);
                $this->addTplParam('showD3PayolutionError', true);
                $this->addTplParam('showD3PayolutionErrorText',$translation);
            }
        }

        $sCountryId = $this->getUser()->getFieldData('oxcountryid');

        /** @var $oCountry Country * */
        $oCountry = oxNew(Country::class);
        if ($oCountry->load($sCountryId) && $oCountry->getFieldData('oxisoalpha2') == 'NL') {
            $this->addTplParam('blD3HeidelpayPayolutionRequirePhone', true);
        }

        return $mReturn;
    }

    /**
     * @param OxidBasket $oxidBasket
     *
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function isHeidelpayPayolutionAllowed(OxidBasket $oxidBasket)
    {
        $currency = $oxidBasket->getBasketCurrency();
        if (false == $this->isPaymentAllowedForCountryAndCurrency('CH', $currency->name)
            && false == $this->isPaymentAllowedForCountryAndCurrency('DE', $currency->name)
            && false == $this->isPaymentAllowedForCountryAndCurrency('AT', $currency->name)
            && false == $this->isPaymentAllowedForCountryAndCurrency('NL', $currency->name)
        ) {
            return false;
        }

        /** @var Payolution $PayolutionPayment */
        $PayolutionPayment = oxNew(Payolution::class);
        $oxPrice           = $oxidBasket->getPrice();
        $price             = $oxPrice->getPrice();
        $minPrice          = $PayolutionPayment->getMinimumLimit();
        $maxPrice          = $PayolutionPayment->getMaximumLimit();

        if (false == ($price >= $minPrice && $maxPrice >= $price)) {
            return false;
        }

        return true;
    }

    /**
     * @param $paymentId
     *
     * @return bool|Payment
     * @throws PaymentNotReferencedToHeidelpayException
     * @throws StandardException
     * @throws \D3\Heidelpay\Models\Settings\Exception\EmptyPaymentlistException
     * @throws \D3\ModCfg\Application\Model\Exception\d3ShopCompatibilityAdapterException
     * @throws \D3\ModCfg\Application\Model\Exception\d3_cfg_mod_exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function d3GetHeidelPayment($paymentId)
    {
        $oxidPayment = oxNew(OxidPayment::class);
        $oxidPayment->load($paymentId);

        $heidelPaySettings = oxNew(Heidelpay::class, d3_cfg_mod::get('d3heidelpay'));
        if (false == $heidelPaySettings->isAssignedToHeidelPayment($oxidPayment)) {
            return false;
        }

        return $heidelPaySettings->getPayment($oxidPayment);
    }

    protected function d3HeidelpaySetErrorMessage(Factory $factory)
    {
        $heidelPayment = $this->d3GetHeidelPayment($this->getD3PaymentId());
        if (false == ($heidelPayment instanceof Payolution)) {
            parent::d3HeidelpaySetErrorMessage($factory);
        }
    }

    /**
     * @param $paymentId
     *
     * @return bool
     * @throws StandardException
     * @throws \D3\ModCfg\Application\Model\Exception\d3ShopCompatibilityAdapterException
     * @throws \D3\ModCfg\Application\Model\Exception\d3_cfg_mod_exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseException
     */
    protected function handleD3HeidelpayPayolution($paymentId)
    {
        $registry  = Registry::get(Registry::class);
        $payolutionTransactionIds = $registry::get(Request::class)->getRequestParameter(
            'd3heidelpayPayolutionTransactionLogid'
        );

        if (false == is_array($payolutionTransactionIds) || empty($payolutionTransactionIds[$paymentId])) {
            // log message
            d3_cfg_mod::get('d3heidelpay')->d3getLog()->log(
                d3log::WARNING,
                __CLASS__,
                __FUNCTION__,
                __LINE__,
                'payolution checkbox not checked but required',
                'user didn\'t checked the configuration text. paymentid: ' . var_export($paymentId, true)
            );
            $registry::getSession()->setVariable('payolutionerror', 'D3HEIDELPAY_PAYMENT_PAYOLUTION_MISSINGCONFIGCHECK');

            return false;
        }

        $birthdate                     = $registry::get(Request::class)->getRequestParameter('d3birthdate');
        $birthdate[$paymentId]['year'] = $birthdate[$paymentId]['Date_Year'];
        if ($this->d3ValidateBirthdateInput($birthdate, $paymentId)) {
            // log message
            d3_cfg_mod::get('d3heidelpay')->d3getLog()->log(
                d3log::WARNING,
                __CLASS__,
                __FUNCTION__,
                __LINE__,
                'birthdate is empty but required',
                'user didn\'t set the birthdate for invoice payment. input: ' . var_export($birthdate, true)
            );
            $registry::getSession()->setVariable('payolutionerror', 'D3HEIDELPAY_PAYMENT_PAYOLUTION_MISSINGBIRTHDATE');

            return false;
        }

        $sBefore18Years     = mktime(0, 0, 0, date('n'), date('j'), date('Y') - 18);
        $birthdateTimestamp = strtotime("{$birthdate[$paymentId]['year']}-{$birthdate[$paymentId]['month']}-{$birthdate[$paymentId]['day']}");
        if ($sBefore18Years < $birthdateTimestamp) {
            // log message
            d3_cfg_mod::get('d3heidelpay')->d3getLog()->log(
                d3log::WARNING,
                __CLASS__,
                __FUNCTION__,
                __LINE__,
                'birthdate is set, but too young',
                'user set birthdate for invoice payment. input: ' . var_export($birthdate, true)
            );
            $registry::getSession()->setVariable('payolutionerror', 'D3HEIDELPAY_PAYMENT_PAYOLUTION_FSK18BIRTHDATE');

            return false;
        }

        $this->getUser()->assign(
            array('oxbirthdate' => $birthdate[$paymentId])
        );

        $sCountryId = $this->getUser()->getFieldData('oxcountryid');

        /** @var $oCountry Country * */
        $oCountry = oxNew(Country::class);
        if ($oCountry->load($sCountryId) && $oCountry->getFieldData('oxisoalpha2') == 'NL') {
            $phone         = $registry::get(Request::class)->getRequestParameter('d3phone');
            $phoneVerifier = oxNew(Phone::class, $registry, $phone, $paymentId);
            if (false == $phoneVerifier->verify()) {
                // log message
                d3_cfg_mod::get('d3heidelpay')->d3getLog()->log(
                    d3log::WARNING,
                    __CLASS__,
                    __FUNCTION__,
                    __LINE__,
                    'phone is empty but required',
                    'user didn\'t set the phone for invoice payment. input: ' . var_export($phone, true)
                );
                $registry::getSession()->setVariable('payolutionerror', 'D3HEIDELPAY_PAYMENT_PAYOLUTION_MISSINGPHONEINPUT');

                return false;
            }

            $this->getUser()->assign(
                array('oxfon' => $phone[$paymentId])
            );
        }

        $this->getUser()->save();

        return 'order';
    }

    /**
     * @param $paymentId
     *
     * @return bool|array
     * @throws PaymentNotReferencedToHeidelpayException
     * @throws StandardException
     * @throws \D3\Heidelpay\Models\Settings\Exception\EmptyPaymentlistException
     * @throws \D3\ModCfg\Application\Model\Exception\d3ShopCompatibilityAdapterException
     * @throws \D3\ModCfg\Application\Model\Exception\d3_cfg_mod_exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseException
     */
    public function sendHeidelpayPayolutionRequest($paymentId)
    {
        if (false == $paymentId) {
            return false;
        }

        $heidelPayment = $this->d3GetHeidelPayment($paymentId);

        if ($heidelPayment instanceof Payolution) {
            $factory = oxNew(Factory::class, d3_cfg_mod::get('d3heidelpay'));
            /** @var RequestPayolution $request */
            $request = oxNew(
                RequestPayolution::class,
                $heidelPayment,
                d3_cfg_mod::get('d3heidelpay'),
                Registry::get(Registry::class),
                $factory
            );
            $response  = $request->sendRequest();

            if ('ACK' !== $response->getResult()) {
                Registry::getSession()->setVariable('payolutionerror', 'D3HEIDELPAY_PAYMENT_PAYOLUTION_STANDARDERROR');

                return false;
            }

            return [
                'configoptintext' => $response->getConfigOptinText(),
                'd3transactionlogid' => $response->getD3transactionlogid()
            ];
        }

        return false;
    }
}
