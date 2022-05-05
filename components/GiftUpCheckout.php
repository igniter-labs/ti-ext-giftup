<?php

namespace IgniterLabs\GiftUp\Components;

use IgniterLabs\GiftUp\Classes\GiftUpOptions;
use IgniterLabs\GiftUp\Models\Settings;
use System\Classes\BaseComponent;

class GiftUpCheckout extends BaseComponent
{
    public function defineProperties()
    {
        return [
            'companyId' => [
                'label' => 'Your company ID. Leave blank to use the default company ID',
                'type' => 'text',
                'validationRule' => 'string',
            ],
            'productId' => [
                'label' => 'Product ID',
                'type' => 'text',
                'validationRule' => 'string',
            ],
            'groupId' => [
                'label' => 'Group ID',
                'type' => 'text',
                'validationRule' => 'string',
            ],
            'language' => [
                'label' => 'Force a specific language',
                'type' => 'text',
                'default' => 'en-GB',
                'placeholder' => 'en-GB',
                'validationRule' => 'string',
            ],
            'purchaserName' => [
                'label' => 'Purchaser\'s name',
                'type' => 'text',
                'validationRule' => 'string',
            ],
            'purchaserEmail' => [
                'label' => 'Purchaser\'s email',
                'type' => 'text',
                'validationRule' => 'string',
            ],
            'recipientName' => [
                'label' => 'Recipient\'s name',
                'type' => 'text',
                'validationRule' => 'string',
            ],
            'recipientEmail' => [
                'label' => 'Recipient\'s email',
                'type' => 'text',
                'validationRule' => 'string',
            ],
            'step' => [
                'label' => 'The default checkout step to display',
                'type' => 'select',
                'default' => 'details',
                'options' => [
                    'details' => 'details',
                    'payment' => 'payment',
                ],
                'validationRule' => 'required|string',
            ],
            'whoFor' => [
                'label' => 'Who the buying experience is for',
                'type' => 'select',
                'options' => [
                    'yourself' => 'yourself',
                    'someoneelse' => 'someoneelse',
                    'onlyme' => 'onlyme',
                    'onlysomeoneelse' => 'onlysomeoneelse',
                ],
                'validationRule' => 'required|string',
            ],
            'promoCode' => [
                'label' => 'Automatically apply a promo code',
                'type' => 'text',
                'validationRule' => 'string',
            ],
            'hideArtwork' => [
                'label' => 'Whether to hide your artwork or not',
                'type' => 'switch',
                'default' => false,
                'validationRule' => 'required|boolean',
            ],
            'hideGroups' => [
                'label' => 'Hide all grouped items. This will leave un-grouped items and the custom value gift cards',
                'type' => 'switch',
                'default' => false,
                'validationRule' => 'required|boolean',
            ],
            'hideUngroupedItems' => [
                'label' => 'Hide all un-grouped items. This will leave all groups of items and the custom value gift cards (if enabled) only.',
                'type' => 'switch',
                'default' => false,
                'validationRule' => 'required|boolean',
            ],
            'hideCustomValue' => [
                'label' => 'Hide custom value gift cards',
                'type' => 'switch',
                'default' => false,
                'validationRule' => 'required|boolean',
            ],
            'customValueAmount' => [
                'label' => 'The custom value amount to display',
                'type' => 'text',
                'validationRule' => 'string',
            ],
        ];
    }

    public function onRun()
    {
        $this->page['errorPage'] = $this->controller->pageUrl($this->property('errorPage'));
        $this->page['successPage'] = $this->controller->pageUrl($this->property('successPage'));

        $this->page['giftUpOptions'] = $this->loadOptions();
    }

    protected function loadOptions()
    {
        return new GiftUpOptions([
            'companyId' => $this->property('companyId') ?: Settings::getCompanyId(),
            'domain' => $this->property('domain'),
            'product' => $this->property('productId'),
            'group' => $this->property('groupId'),
            'language' => $this->property('language') ?: 'en-GB',
            'purchaserName' => $this->property('purchaserName'),
            'purchaserEmail' => $this->property('purchaserEmail'),
            'recipientName' => $this->property('recipientName'),
            'recipientEmail' => $this->property('recipientEmail'),
            'step' => $this->property('step'),
            'whoFor' => $this->property('whoFor'),
            'promoCode' => $this->property('promoCode'),
            'hideArtwork' => $this->property('hideArtwork'),
            'hideGroups' => $this->property('hideGroups'),
            'hideUngroupedItems' => $this->property('hideUngroupedItems'),
            'hideCustomValue' => $this->property('hideCustomValue'),
            'customValueAmount' => $this->property('customValueAmount'),
        ]);
    }
}
