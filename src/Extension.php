<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp;

use Igniter\Cart\Models\Order;
use Igniter\System\Classes\BaseExtension;
use IgniterLabs\GiftUp\CartConditions\RedeemGiftCard;
use IgniterLabs\GiftUp\Classes\Manager;
use IgniterLabs\GiftUp\Components\GiftUpCheckout;
use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Event;
use Override;

/**
 * GiftUp Extension Information File
 */
class Extension extends BaseExtension
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(Manager::class);
    }

    #[Override]
    public function boot(): void
    {
        Event::listen('igniter.cart.beforeApplyCoupon', function($code) {
            if (Settings::isConnected()) {
                return resolve(Manager::class)->applyGiftCardCode($code);
            }
        });

        Event::listen('igniter.checkout.beforePayment', function(Order $order, $data): void {
            if (Settings::isConnected()) {
                resolve(Manager::class)->redeemGiftCard($order);
            }
        });
    }

    #[Override]
    public function registerPermissions(): array
    {
        return [
            'IgniterLabs.GiftUp.ManageSettings' => [
                'description' => 'lang:igniterlabs.giftup::default.help_permission',
                'group' => 'igniter.cart::default.text_permission_order_group',
            ],
        ];
    }

    #[Override]
    public function registerSettings(): array
    {
        return [
            'settings' => [
                'label' => 'lang:igniterlabs.giftup::default.text_settings',
                'description' => 'lang:igniterlabs.giftup::default.help_settings',
                'icon' => 'fa fa-gear',
                'model' => Settings::class,
                'permissions' => ['IgniterLabs.GiftUp.ManageSettings'],
            ],
        ];
    }

    #[Override]
    public function registerComponents(): array
    {
        return [
            GiftUpCheckout::class => [
                'code' => 'giftUpCheckout',
                'name' => 'lang:igniterlabs.giftup::default.text_component',
                'description' => 'lang:igniterlabs.giftup::default.help_component',
            ],
        ];
    }

    public function registerCartConditions(): array
    {
        return [
            RedeemGiftCard::class => [
                'name' => 'giftup',
                'label' => 'lang:igniterlabs.giftup::default.text_cart_condition',
                'description' => 'lang:igniterlabs.giftup::default.help_cart_condition',
            ],
        ];
    }
}
