<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp\Tests\Classes;

use Igniter\Cart\Facades\Cart;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\PayRegister\Models\Payment;
use IgniterLabs\GiftUp\CartConditions\RedeemGiftCard;
use IgniterLabs\GiftUp\Classes\Manager;
use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

it('applies a gift card code to the cart', function(): void {
    Settings::set('api_key', 'test-key');
    Http::fake([
        'https://api.giftup.app/*' => Http::response([
            'backingType' => 'Currency',
            'hasExpired' => false,
            'notYetValid' => false,
            'canBeRedeemed' => true,
            'remainingValue' => 100,
        ]),
    ]);

    $code = 'TESTCODE';
    $redeemGiftCard = new RedeemGiftCard([
        'label' => 'GiftUp',
        'metaData' => ['code' => $code],
    ]);

    Cart::shouldReceive('getCondition')->andReturn($redeemGiftCard);
    Cart::shouldReceive('loadCondition')->once();

    $result = (new Manager)->applyGiftCardCode($code);

    expect($result?->getMetaData('code'))->toBe($code);
});

it('does not apply gift card code when cart condition is missing', function(): void {
    $code = 'TESTCODE';
    Cart::shouldReceive('getCondition')->andReturnNull();
    Cart::shouldReceive('loadCondition')->never();

    (new Manager)->applyGiftCardCode($code);
});

it('does not apply gift card code when api key is missing', function(): void {
    Settings::clearInternalCache();
    (new Manager)->clearInternalCache();
    $code = 'TESTCODE';
    $redeemGiftCard = new RedeemGiftCard([
        'label' => 'GiftUp',
        'metaData' => ['code' => $code],
    ]);

    Cart::shouldReceive('getCondition')->andReturn($redeemGiftCard);
    Cart::shouldReceive('loadCondition')->never();

    expect((new Manager)->applyGiftCardCode($code))->toBeNull();
});

it('throws an exception if the gift card code is invalid', function(): void {
    Settings::set('api_key', 'test-key');
    Settings::set('is_live', 'staging');
    Http::fake([
        'https://api.giftup.app/*' => Http::response(['error' => 'Invalid code'], 404),
    ]);

    $code = 'INVALIDCODE';
    $redeemGiftCard = new RedeemGiftCard([
        'label' => 'GiftUp',
        'metaData' => ['code' => $code],
    ]);

    Cart::shouldReceive('getCondition')->andReturn($redeemGiftCard)->once();
    Log::partialMock()->shouldReceive('error')->atLeast(1);

    (new Manager)->applyGiftCardCode($code);
});

it('throws an exception if the gift card has an invalid type', function(): void {
    $giftCard = (object)[
        'backingType' => 'NonCurrency',
        'hasExpired' => false,
        'notYetValid' => false,
        'canBeRedeemed' => true,
        'remainingValue' => 100,
    ];

    expect(fn() => (new Manager)->validateGiftCard($giftCard))
        ->toThrow(ApplicationException::class, lang('igniterlabs.giftup::default.alert_gift_card_invalid_type'));
});

it('throws an exception if the gift card has expired', function(): void {
    $giftCard = (object)[
        'backingType' => 'Currency',
        'hasExpired' => true,
        'notYetValid' => false,
        'canBeRedeemed' => true,
        'remainingValue' => 100,
    ];

    expect(fn() => (new Manager)->validateGiftCard($giftCard))
        ->toThrow(ApplicationException::class, lang('igniterlabs.giftup::default.alert_gift_card_expired'));
});

it('throws an exception if the gift card is not yet valid', function(): void {
    $giftCard = (object)[
        'backingType' => 'Currency',
        'hasExpired' => false,
        'notYetValid' => true,
        'canBeRedeemed' => true,
        'remainingValue' => 100,
    ];

    expect(fn() => (new Manager)->validateGiftCard($giftCard))
        ->toThrow(ApplicationException::class, lang('igniterlabs.giftup::default.alert_gift_card_invalid'));
});

it('throws an exception if the gift card cannot be redeemed', function(): void {
    $giftCard = (object)[
        'backingType' => 'Currency',
        'hasExpired' => false,
        'notYetValid' => false,
        'canBeRedeemed' => false,
        'remainingValue' => 100,
    ];

    expect(fn() => (new Manager)->validateGiftCard($giftCard))
        ->toThrow(ApplicationException::class, lang('igniterlabs.giftup::default.alert_gift_card_redeemed'));
});

it('throws an exception if the gift card balance is too low', function(): void {
    Settings::set('minimum_value', 50);

    $giftCard = (object)[
        'backingType' => 'Currency',
        'hasExpired' => false,
        'notYetValid' => false,
        'canBeRedeemed' => true,
        'remainingValue' => 30,
    ];

    expect(fn() => (new Manager)->validateGiftCard($giftCard))
        ->toThrow(ApplicationException::class, lang('igniterlabs.giftup::default.alert_gift_card_balance_low'));
});

it('redeems a gift card successfully', function(): void {
    Settings::set('api_key', 'test-key');
    Http::fake([
        'https://api.giftup.app/*' => Http::sequence()
            ->push([
                'backingType' => 'Currency',
                'hasExpired' => false,
                'notYetValid' => false,
                'canBeRedeemed' => true,
                'remainingValue' => 300,
            ])
            ->push(['success' => true]),
    ]);

    $order = mock(Order::class)->makePartial();
    $order->order_id = 123;
    $order->setRelation('payment_method', new Payment);
    $order->shouldReceive('isPaymentProcessed')->andReturn(false);
    $order->shouldReceive('logPaymentAttempt')->once();

    Cart::add([
        'id' => 1,
        'name' => 'Test Item',
        'price' => 100.00,
    ], 1);

    $redeemGiftCard = new RedeemGiftCard([
        'label' => 'GiftUp',
        'metaData' => ['code' => 'TESTCODE'],
    ]);
    $redeemGiftCard->onLoad();

    Cart::shouldReceive('conditions')->andReturn(collect(['giftup' => $redeemGiftCard]));

    (new Manager)->redeemGiftCard($order);
});

it('does not redeem a gift card if no condition exists', function(): void {
    $order = mock(Order::class)->makePartial();
    $order->shouldReceive('isPaymentProcessed')->never();

    Cart::shouldReceive('conditions')->andReturn(collect([]))->once();

    (new Manager)->redeemGiftCard($order);
});

it('does not redeem a gift card if the code is missing', function(): void {
    $order = mock(Order::class)->makePartial();
    $order->shouldReceive('isPaymentProcessed')->never();

    $redeemGiftCard = new RedeemGiftCard([
        'label' => 'GiftUp',
        'metaData' => ['code' => ''],
    ]);

    Cart::shouldReceive('conditions')->andReturn(collect(['giftup' => $redeemGiftCard]))->once();

    (new Manager)->redeemGiftCard($order);
});

it('throws an exception if the order is already processed', function(): void {
    $order = mock(Order::class)->makePartial();
    $order->shouldReceive('isPaymentProcessed')->andReturn(true);

    $redeemGiftCard = new RedeemGiftCard([
        'label' => 'GiftUp',
        'metaData' => ['code' => 'TESTCODE'],
    ]);

    Cart::shouldReceive('conditions')->andReturn(collect(['giftup' => $redeemGiftCard]));

    expect(fn() => (new Manager)->redeemGiftCard($order))
        ->toThrow(ApplicationException::class, lang('igniterlabs.giftup::default.alert_order_not_processed'));
});
