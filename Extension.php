<?php

namespace IgniterLabs\GiftUp;

use Admin\Models\Orders_model;
use IgniterLabs\GiftUp\Classes\Manager;
use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Event;
use System\Classes\BaseExtension;

/**
 * GiftUp Extension Information File
 */
class Extension extends BaseExtension
{
    public function boot()
    {
        Event::listen('igniter.cart.beforeApplyCoupon', function ($code) {
            if (Settings::isConnected())
                return Manager::instance()->applyGiftCardCode($code);
        });

        Event::listen('igniter.checkout.beforePayment', function (Orders_model $order, $data) {
            if (Settings::isConnected())
                Manager::instance()->redeemGiftCard($order);
        });
    }

    public function registerPermissions()
    {
        return [
            'IgniterLabs.GiftUp.ManageSettings' => [
                'description' => 'lang:igniterlabs.giftup::default.help_permission',
                'group' => 'module',
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'lang:igniterlabs.giftup::default.text_settings',
                'description' => 'lang:igniterlabs.giftup::default.help_settings',
                'icon' => 'fa fa-gear',
                'model' => \IgniterLabs\GiftUp\Models\Settings::class,
                'permissions' => ['IgniterLabs.GiftUp.ManageSettings'],
            ],
        ];
    }

    public function registerComponents()
    {
        return [
            \IgniterLabs\GiftUp\Components\GiftUpCheckout::class => [
                'code' => 'giftUpCheckout',
                'name' => 'lang:igniterlabs.giftup::default.text_component',
                'description' => 'lang:igniterlabs.giftup::default.help_component',
            ],
        ];
    }

    public function registerCartConditions()
    {
        return [
            \IgniterLabs\GiftUp\CartConditions\RedeemGiftCard::class => [
                'name' => 'giftup',
                'label' => 'lang:igniterlabs.giftup::default.text_cart_condition',
                'description' => 'lang:igniterlabs.giftup::default.help_cart_condition',
            ],
        ];
    }
}



