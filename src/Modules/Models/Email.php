<?php
/**
 * This Software is the property of Data Development and is protected
 * by copyright law - it is NOT Freeware.
 * Any unauthorized use of this software without a valid license
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 * http://www.shopmodule.com
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author        D3 Data Development <support@shopmodule.com>
 * @link          http://www.oxidmodule.com
 */

namespace D3\Hppayolution\Modules\Models;

use D3\Heidelpay\Models\Settings\Heidelpay;
use D3\Hppayolution\Models\Payment\Payolution;
use D3\ModCfg\Application\Model\Configuration\d3_cfg_mod;
use Doctrine\DBAL\DBALException;
use OxidEsales\Eshop\Core\Exception\StandardException;

/**
 * class Email
 */
class Email extends Email_parent
{
    protected $d3HppayolutionBccAddress;

    /**
     * @param \D3\Heidelpay\Modules\Application\Model\Order $order
     * @param null                                          $subject
     *
     * @return bool
     * @throws DBALException
     */
    public function sendOrderEmailToUser($order, $subject = null)
    {
        // loading payment object
        $paymentId   = $order->getFieldData('oxpaymenttype');
        $oxidPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

        if (false == $oxidPayment->load($paymentId)) {
            return parent::sendOrderEmailToUser($order, $subject);
        }

        try {
            /** @var Heidelpay $heidelPaySettings */
            $heidelPaySettings = oxNew(Heidelpay::class, d3_cfg_mod::get('d3heidelpay'));
            if (false == $heidelPaySettings->isAssignedToHeidelPayment($oxidPayment)) {
                return parent::sendOrderEmailToUser($order, $subject);
            }

            $heidelpayment = $heidelPaySettings->getPayment($oxidPayment);
            if ($heidelpayment instanceof Payolution) {
                $this->d3HppayolutionBccAddress = d3_cfg_mod::get('d3heidelpay')->getValue('payolutionEMail');
                if (d3_cfg_mod::get('d3heidelpay')->getValue('d3heidelpay_blTestmode') || d3_cfg_mod::get('d3heidelpay')->isDemo()) {
                    $this->d3HppayolutionBccAddress = d3_cfg_mod::get('d3heidelpay')->getValue('payolutionTestEMail');
                }
            }
        } catch (StandardException $e) {
            return parent::sendOrderEmailToUser($order, $subject);
        }

        $return                         = parent::sendOrderEmailToUser($order, $subject);
        $this->d3HppayolutionBccAddress = null;

        return $return;
    }

    /**
     * @param null $address
     * @param null $name
     */
    public function setRecipient($address = null, $name = null)
    {
        if ($this->d3HppayolutionBccAddress) {
            $this->addBCC($this->d3HppayolutionBccAddress);
        }
        parent::setRecipient($address, $name);
    }
}
