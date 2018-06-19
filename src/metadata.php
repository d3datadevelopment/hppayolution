<?php

use D3\Heidelpay\Controllers as HeidelpayController;
use D3\Heidelpay\Models\Containers\PrepaymentData;
use D3\Heidelpay\Models\Factory as HeidelpayFactory;
use D3\Heidelpay\Models\Settings\Heidelpay as HeidelpaySettings;
use D3\Hppayolution\Modules\Controller;
use D3\Hppayolution\Modules\Models;
use D3\ModCfg\Application\Model\d3utils;
use OxidEsales\Eshop\Application\Controller as OxidController;
use OxidEsales\Eshop\Application\Model\Order as OxidOrder;
use OxidEsales\Eshop\Core\Email as OxidEmail;

$sMetadataVersion = '2.0';

$aModule = array(
    'id'             => 'hppayolution',
    'title'          => (class_exists(d3utils::class) ? d3utils::getInstance()->getD3Logo() : 'D&sup3;') . ' Heidelpay Payolution Addon',
    'description'    => array(
        'de' => 'Das Modul ist ein Addon für das D³ Heidelpay Modul.<br>Die Integration der Premium Zahlungsart "Payolution".<br>Aktuell ist das Modul lediglich im Theme Flow oder einen Abkömmling einsetzbar.',
        'en' => '',
    ),
    'thumbnail'      => 'picture.png',
    'version'        => '1.0.0.0',
    'author'         => 'D&sup3; Data Development, Inh. Thomas Dartsch',
    'email'          => 'support@shopmodule.com',
    'url'            => 'http://www.oxidmodule.com/',
    'events'         => array(),
    'd3SetupClasses' => array(),
    'extend'         => array(
        HeidelpayFactory::class                 => Models\Factory::class,
        HeidelpaySettings::class                => Models\Settings::class,
        PrepaymentData::class                   => Models\Prepaymentdata::class,
        OxidEmail::class                        => Models\Email::class,
        OxidOrder::class                        => Models\Order::class,
        OxidController\PaymentController::class => Controller\PaymentController::class,
        HeidelpayController\Order::class        => Controller\HeidelpayOrder::class,
    ),
    'controllers'    => array(
        'd3_hppayolution_controllers_admin_settings' => \D3\Hppayolution\Controllers\Admin\Settings::class,
    ),
    'templates'      => array(
        'd3_hppayolution_views_admin_tpl_settings.tpl'          => 'd3/hppayolution/views/admin/tpl/settings.tpl',
        'd3_hppayolution_views_flow_tpl_payment_payolution.tpl' => 'd3/hppayolution/views/flow/tpl/payment/payolution.tpl',
    ),
    'blocks'         => array(

        array(
            'template' => 'page/checkout/payment.tpl',
            'block'    => 'checkout_payment_errors',
            'file'     => '/views/blocks/checkout_payment_errors.tpl'
        ),
    ),
);
