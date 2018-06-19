<?php

namespace D3\Hppayolution\Modules\Models;

use D3\Heidelpay\Models\Containers\PrepaymentData;
use D3\Heidelpay\Models\Payment\Exception\PaymentNotReferencedToHeidelpayException;
use D3\Heidelpay\Models\Settings\Heidelpay;
use D3\Hppayolution\Models\Payment\Payolution;
use D3\ModCfg\Application\Model\Configuration\d3_cfg_mod;
use OxidEsales\Eshop\Application\Model\Payment as OxidPayment;

/**
 */
class Order extends Order_parent
{

    /**
     * Returns bank transfer data if available
     *
     * @return \stdClass|null
     * @throws PaymentNotReferencedToHeidelpayException
     * @throws \D3\ModCfg\Application\Model\Exception\d3ShopCompatibilityAdapterException
     * @throws \D3\ModCfg\Application\Model\Exception\d3_cfg_mod_exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    public function getHeidelpayBankTransferData()
    {
        if (false == d3_cfg_mod::get('d3heidelpay')->isActive()) {
            return null;
        }

        /** @var Heidelpay $oSettings */
        /** @var OxidPayment $oPayment */
        $oSettings = oxNew(Heidelpay::class, d3_cfg_mod::get('d3heidelpay'));
        $oPayment  = oxNew(OxidPayment::class);
        $oPayment->load($this->getFieldData('oxpaymenttype'));
        if (false == $oSettings->isAssignedToHeidelPayment($oPayment)) {
            return null;
        }

        $oHeidelpayment = $oSettings->getPayment($oPayment);

        if ($oHeidelpayment instanceof Payolution) {
            /** @var PrepaymentData $oPrePaymentData */
            $oPrePaymentData = oxNew(PrepaymentData::class);

            return $oPrePaymentData->getBankTransferData($this, 'IV.PA');
        }

        return parent::getHeidelpayBankTransferData();
    }

}
