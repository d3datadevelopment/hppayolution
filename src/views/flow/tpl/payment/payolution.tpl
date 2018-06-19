[{assign var='oHeidelPaySettings' value=$oHeidelpayViewConfig->getSettings()}]
[{assign var='oHeidelPayment' value=$oHeidelPaySettings->getPayment($paymentmethod)}]
[{assign var="responseParameter" value=$oView->sendHeidelpayPayolutionRequest($sPaymentID)}]
[{assign var='disableFields' value="disabled"}]

[{if $blD3HeidelpayHasSameAdresses && $blD3HeidelpayAllowPayolution}]
    [{assign var='disableFields' value=""}]
[{/if}]

[{block name="heidelpay_payolution"}]
    [{assign var="iBirthdayMonth" value=0}]
    [{assign var="iBirthdayDay" value=0}]
    [{assign var="iBirthdayYear" value=0}]

    [{if $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00" && false == is_array($oxcmp_user->oxuser__oxbirthdate->value)}]
        [{assign var="iBirthdayMonth" value=$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:"/^([0-9]{4})[-]/":""|regex_replace:'/[-]([0-9]{1,2})$/':""}]
        [{assign var="iBirthdayDay" value=$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:"/^([0-9]{4})[-]([0-9]{1,2})[-]/":""}]
        [{assign var="iBirthdayYear" value=$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:'/[-]([0-9]{1,2})[-]([0-9]{1,2})$/':""}]
        [{assign var="birthdate" value=$oxcmp_user->oxuser__oxbirthdate->value}]
    [{elseif is_array($oxcmp_user->oxuser__oxbirthdate->value)}]
        [{assign var="iBirthdayMonth" value=$oxcmp_user->oxuser__oxbirthdate->value.month}]
        [{assign var="iBirthdayDay" value=$oxcmp_user->oxuser__oxbirthdate->value.day}]
        [{assign var="iBirthdayYear" value=$oxcmp_user->oxuser__oxbirthdate->value.year}]
        [{assign var="birthdate" value="`$iBirthdayYear`-`$iBirthdayMonth`-`$iBirthdayDay`"}]
    [{/if}]

    <dl>
        <dt>
            <input type="radio"
                    [{if $blD3HeidelpayHasSameAdresses && $blD3HeidelpayAllowPayolution}]
                        id="payment_[{$sPaymentID}]"
                        name="paymentid"
                        value="[{$sPaymentID}]"
                        [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]
                    [{else}]
                        [{$disableFields}]
                    [{/if}]
            >
            <label for="payment_[{$sPaymentID}]">
                <b>[{$paymentmethod->oxpayments__oxdesc->value}]</b>
            </label>
            [{if false == $blD3HeidelpayHasSameAdresses}]
                <sup class="alert alert-danger d3HeidelaySameAddressNotice">[{oxmultilang ident="D3HEIDELPAY_PAYMENT_NOTSAMEADDRESS_NOTICE"}]</sup>
            [{/if}]
            [{if false == $blD3HeidelpayAllowPayolution}]
                [{assign_adv var="d3PayolutionLimits" value='array("'|cat:$oHeidelPayment->getMinimumLimit()|cat:'", "'|cat:$oHeidelPayment->getMaximumLimit()|cat:'")'}]
                <sup id="d3HeidelayPayolutionNotice"
                     class="alert alert-danger desc d3HeidelaySameAddressNotice">[{oxmultilang ident="D3HEIDELPAY_PAYMENT_PAYOLUTION_NOTICE" args=$d3PayolutionLimits}]</sup>
            [{/if}]
        </dt>
        <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
            [{if $paymentmethod->getPrice()}]
                [{assign var="oPaymentPrice" value=$paymentmethod->getPrice()}]
                [{if $oViewConf->isFunctionalityEnabled('blShowVATForPayCharge')}]
                    ([{oxprice price=$oPaymentPrice->getNettoPrice() currency=$currency}]
                    [{if $oPaymentPrice->getVatValue() > 0}]
                        [{oxmultilang ident="PLUS_VAT"}] [{oxprice price=$oPaymentPrice->getVatValue() currency=$currency}]
                    [{/if}])
                [{else}]
                    ([{oxprice price=$oPaymentPrice->getBruttoPrice() currency=$currency}])
                [{/if}]
            [{/if}]

            <div class="form-group oxDate">
                <label class="control-label col-xs-12 col-lg-3 req">
                    [{oxmultilang ident="BIRTHDATE"}]
                    [{if $oView->getPaymentError() == 1}]*[{/if}]
                </label>
                <div class="col-xs-3 col-lg-3">
                    <select class="oxDay form-control" name="d3birthdate[[{$sPaymentID}]][day]"
                           [{$disableFields}] required="">
                        <option value="" label="-">-</option>
                        [{section name="day" start=1 loop=32}]
                            <option value="[{$smarty.section.day.index}]"
                                    label="[{$smarty.section.day.index}]"
                                    [{if $iBirthdayDay == $smarty.section.day.index}] selected="selected" [{/if}]>
                                [{$smarty.section.day.index}]
                            </option>
                        [{/section}]
                    </select>
                </div>
                <div class="col-xs-6 col-lg-3">
                    <select class="oxMonth form-control" name="d3birthdate[[{$sPaymentID}]][month]"
                            [{$disableFields}] required="">
                        <option value="" label="-">-</option>
                        [{section name="month" start=1 loop=13}]
                            <option value="[{$smarty.section.month.index}]"
                                    label="[{$smarty.section.month.index}]"
                                    [{if $iBirthdayMonth == $smarty.section.month.index}] selected="selected" [{/if}]>
                                [{oxmultilang ident="MONTH_NAME_"|cat:$smarty.section.month.index}]
                            </option>
                        [{/section}]
                    </select>
                </div>
                <div class="col-xs-3 col-lg-3">
                    [{html_select_date field_array="d3birthdate[`$sPaymentID`]" start_year='-80' time=$birthdate reverse_years=true
                    end_year='-18' display_days=false display_months=false all_extra='class="oxYear form-control" required=""'|cat:$disableFields
                    year_empty="-"}]
                </div>
            </div>
            [{if $blD3HeidelpayPayolutionRequirePhone}]
                <div class="form-group">
                    <label class="control-label col-lg-3 req">[{oxmultilang ident="PHONE"}]</label>
                    <div class="col-lg-9">
                        <input class="form-control js-oxValidate js-oxValidate_notEmpty" type="text" size="37"
                               maxlength="128" name="d3phone[[{$sPaymentID}]]"
                               value="[{if $oxcmp_user->getFieldData('oxfon')}][{$oxcmp_user->getFieldData('oxfon')}][{else}][{$oxcmp_user->oxuser__oxfon->value}][{/if}]"
                               [{$disableFields}] required="">
                    </div>
                </div>
            [{/if}]
            <div class="alert alert-info">[{oxmultilang ident="COMPLETE_MARKED_FIELDS"}]</div>
            <div class="alert alert-info desc">
                <input type="hidden" name="d3heidelpayPayolutionTransactionLogid[[{$sPaymentID}]]" value="0" [{$disableFields}]/>
                <input type="checkbox" name="d3heidelpayPayolutionTransactionLogid[[{$sPaymentID}]]"
                        [{$disableFields}] value="[{$responseParameter.d3transactionlogid}]"/>
                [{$responseParameter.configoptintext}]
            </div>

            [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                <div class="alert alert-info desc">
                    [{$paymentmethod->oxpayments__oxlongdesc->value}]
                </div>
            [{/if}]
        </dd>
    </dl>
[{/block}]
