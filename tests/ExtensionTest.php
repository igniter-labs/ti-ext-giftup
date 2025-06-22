<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp\Tests;

use Closure;
use Igniter\Cart\Models\Order;
use IgniterLabs\GiftUp\CartConditions\RedeemGiftCard;
use IgniterLabs\GiftUp\Classes\Manager;
use IgniterLabs\GiftUp\Components\GiftUpCheckout;
use IgniterLabs\GiftUp\Extension;
use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Event;

it('registers the extension', function(): void {
    (new Extension(app()))->register();

    expect(app()->bound(Manager::class))->toBeTrue();
});

it('boots the extension', function(): void {
    Event::fake();

    (new Extension(app()))->boot();

    Event::assertListening('igniter.cart.beforeApplyCoupon', Closure::class);
    Event::assertListening('igniter.checkout.beforePayment', Closure::class);
});

it('handles igniter.cart.beforeApplyCoupon events correctly', function(): void {
    Settings::set('api_key', 'test-key');
    Settings::set('company_info', ['id' => '12345']);
    $managerMock = mock(Manager::class);
    app()->instance(Manager::class, $managerMock);
    $managerMock->shouldReceive('applyGiftCardCode')->with('TEST')->once();

    Event::dispatch('igniter.cart.beforeApplyCoupon', ['TEST']);
});

it('handles igniter.checkout.beforePayment events correctly', function(): void {
    Settings::set('api_key', 'test-key');
    Settings::set('company_info', ['id' => '12345']);
    $order = Order::factory()->create();
    $managerMock = mock(Manager::class);
    app()->instance(Manager::class, $managerMock);
    $managerMock->shouldReceive('redeemGiftCard')->with($order)->once();

    Event::dispatch('igniter.checkout.beforePayment', [$order, []]);
});

it('registers permissions', function(): void {
    $extension = new Extension(app());
    $permissions = $extension->registerPermissions();

    expect($permissions)
        ->toHaveKey('IgniterLabs.GiftUp.ManageSettings')
        ->and($permissions['IgniterLabs.GiftUp.ManageSettings']['description'])
        ->toBe('lang:igniterlabs.giftup::default.help_permission')
        ->and($permissions['IgniterLabs.GiftUp.ManageSettings']['group'])
        ->toBe('igniter.cart::default.text_permission_order_group');
});

it('registers settings', function(): void {
    $extension = new Extension(app());
    $settings = $extension->registerSettings();

    expect($settings)
        ->toHaveKey('settings')
        ->and($settings['settings']['label'])
        ->toBe('lang:igniterlabs.giftup::default.text_settings')
        ->and($settings['settings']['model'])
        ->toBe(Settings::class)
        ->and($settings['settings']['permissions'])
        ->toBe(['IgniterLabs.GiftUp.ManageSettings']);
});

it('registers components', function(): void {
    $extension = new Extension(app());
    $components = $extension->registerComponents();

    expect($components)
        ->toHaveKey(GiftUpCheckout::class)
        ->and($components[GiftUpCheckout::class]['code'])
        ->toBe('giftUpCheckout');
});

it('registers cart conditions', function(): void {
    $extension = new Extension(app());
    $conditions = $extension->registerCartConditions();

    expect($conditions)
        ->toHaveKey(RedeemGiftCard::class)
        ->and($conditions[RedeemGiftCard::class]['name'])
        ->toBe('giftup');
});
