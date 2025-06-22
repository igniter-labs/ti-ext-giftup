<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp\Tests\CartConditions;

use Igniter\Cart\Facades\Cart;
use IgniterLabs\GiftUp\CartConditions\RedeemGiftCard;
use IgniterLabs\GiftUp\Classes\Manager;
use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Http;

beforeEach(function(): void {
    $this->redeemGiftCard = new RedeemGiftCard([
        'label' => 'GiftUp',
        'metaData' => ['code' => 'TEST'],
    ]);
});

afterEach(function(): void {
    $this->redeemGiftCard->clearMetaData();
    resolve(Manager::class)->clearInternalCache();
});

it('gets label correctly', function(): void {
    $this->redeemGiftCard->beforeApply();

    expect($this->redeemGiftCard->getLabel())->toBe('GiftUp TEST')
        ->and($this->redeemGiftCard->getModel())->toBeNull();
});

it('gets value correctly', function(): void {
    Settings::set('api_key', 'test-key');
    Http::fake([
        'https://api.giftup.app/*' => Http::response([
            'backingType' => 'Currency',
            'hasExpired' => false,
            'notYetValid' => false,
            'canBeRedeemed' => true,
            'remainingValue' => 300,
        ]),
    ]);

    Cart::add([
        'id' => 1,
        'name' => 'Test Item',
        'price' => 100.00,
    ], 1);

    $this->redeemGiftCard->onLoad();
    $this->redeemGiftCard->calculate(20);

    expect($this->redeemGiftCard->getValue())->toBe(-100.0);
});

it('loads condition skips when giftup voucher code does not exists', function(): void {
    $redeemGiftCard = new RedeemGiftCard([
        'label' => 'GiftUp',
        'metaData' => ['code' => ''],
    ]);
    $redeemGiftCard->onLoad();

    expect(flash()->messages()->first())->toBeNull()
        ->and($redeemGiftCard->getActions())->toBe([['value' => 0]]);
});

it('loads condition flashes error when giftup voucher has expired', function(): void {
    Settings::set('api_key', 'test-key');
    Http::fake([
        'https://api.giftup.app/*' => Http::response([
            'backingType' => 'Currency',
            'hasExpired' => true,
            'notYetValid' => false,
            'canBeRedeemed' => true,
            'remainingValue' => 300,
        ]),
    ]);

    Cart::add([
        'id' => 1,
        'name' => 'Test Item',
        'price' => 100.00,
    ], 1);

    $this->redeemGiftCard->onLoad();

    expect(flash()->messages()->first())->level->toBe('info')
        ->message->toBe(lang('igniterlabs.giftup::default.alert_gift_card_expired'))
        ->and($this->redeemGiftCard->getMetaData('code'))->toBeNull();
});

it('loads condition successfully', function(): void {
    Settings::set('api_key', 'test-key');
    Http::fake([
        'https://api.giftup.app/*' => Http::response([
            'backingType' => 'Currency',
            'hasExpired' => false,
            'notYetValid' => false,
            'canBeRedeemed' => true,
            'remainingValue' => 300,
        ]),
    ]);

    Cart::add([
        'id' => 1,
        'name' => 'Test Item',
        'price' => 100.00,
    ], 1);

    $this->redeemGiftCard->onLoad();
    $this->redeemGiftCard->onLoad(); // test cache
    $this->redeemGiftCard->beforeApply();

    expect(flash()->messages())->toBeEmpty();
});

it('gets actions correctly when giftup voucher value is greater than cart subtotal', function(): void {
    Settings::set('api_key', 'test-key');
    Http::fake([
        'https://api.giftup.app/*' => Http::response([
            'backingType' => 'Currency',
            'hasExpired' => false,
            'notYetValid' => false,
            'canBeRedeemed' => true,
            'remainingValue' => 300,
        ]),
    ]);

    Cart::add([
        'id' => 1,
        'name' => 'Test Item',
        'price' => 400.00,
    ], 1);

    $this->redeemGiftCard->onLoad();

    expect($this->redeemGiftCard->getActions())->toBe([['value' => -300]]);
});

it('gets actions correctly when giftup voucher value is less than cart subtotal', function(): void {
    Settings::set('api_key', 'test-key');
    Http::fake([
        'https://api.giftup.app/*' => Http::response([
            'backingType' => 'Currency',
            'hasExpired' => false,
            'notYetValid' => false,
            'canBeRedeemed' => true,
            'remainingValue' => 300,
        ]),
    ]);

    Cart::add([
        'id' => 1,
        'name' => 'Test Item',
        'price' => 200.00,
    ], 1);

    $this->redeemGiftCard->onLoad();

    expect($this->redeemGiftCard->getActions())->toBe([['value' => -200.0]]);
});
