<?php

namespace IgniterLabs\GiftUp;

use Igniter\Cart\Models\Order;
use Igniter\System\Classes\BaseExtension;
use IgniterLabs\GiftUp\Classes\Manager;
use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Event;

/**
 * GiftUp Extension Information File
 */
class Extension extends BaseExtension
{
    public function register()
    {
        $this->app->singleton(Manager::class);
    }

    public function boot()
    {
        Event::listen('igniter.cart.beforeApplyCoupon', function($code) {
            if (Settings::isConnected()) {
                return resolve(Manager::class)->applyGiftCardCode($code);
            }
        });

        Event::listen('igniter.checkout.beforePayment', function(Order $order, $data) {
            if (Settings::isConnected()) {
                resolve(Manager::class)->redeemGiftCard($order);
            }
        });
    }

    public function registerPermissions(): array
    {
        return [
            'IgniterLabs.GiftUp.ManageSettings' => [
                'description' => 'lang:igniterlabs.giftup::default.help_permission',
                'group' => 'igniter.cart::default.text_permission_order_group',
            ],
        ];
    }

    public function registerSettings(): array
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

    public function registerComponents(): array
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

