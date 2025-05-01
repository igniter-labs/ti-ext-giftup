<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp\Tests\Components;

use Igniter\Main\Template\Code\PageCode;
use Igniter\Main\Template\Page;
use IgniterLabs\GiftUp\Classes\GiftUpOptions;
use IgniterLabs\GiftUp\Components\GiftUpCheckout;
use IgniterLabs\GiftUp\Models\Settings;

it('defines properties correctly', function(): void {
    $component = new GiftUpCheckout;

    $properties = $component->defineProperties();

    expect($properties)
        ->toHaveKey('companyId.type', 'text')
        ->toHaveKey('companyId.validationRule', 'nullable|string')
        ->toHaveKey('productId.type', 'text')
        ->toHaveKey('productId.validationRule', 'nullable|string')
        ->toHaveKey('groupId.type', 'text')
        ->toHaveKey('groupId.validationRule', 'nullable|string')
        ->toHaveKey('language.type', 'text')
        ->toHaveKey('language.default', 'en-GB')
        ->toHaveKey('language.placeholder', 'en-GB')
        ->toHaveKey('language.validationRule', 'nullable|string')
        ->toHaveKey('purchaserName.type', 'text')
        ->toHaveKey('purchaserName.validationRule', 'nullable|string')
        ->toHaveKey('purchaserEmail.type', 'text')
        ->toHaveKey('purchaserEmail.validationRule', 'nullable|string')
        ->toHaveKey('recipientName.type', 'text')
        ->toHaveKey('recipientName.validationRule', 'nullable|string')
        ->toHaveKey('recipientEmail.type', 'text')
        ->toHaveKey('recipientEmail.validationRule', 'nullable|string')
        ->toHaveKey('step.type', 'select')
        ->toHaveKey('step.default', 'details')
        ->toHaveKey('step.options', [
            'details' => 'details',
            'payment' => 'payment',
        ])
        ->toHaveKey('step.validationRule', 'required|string')
        ->toHaveKey('whoFor.type', 'select')
        ->toHaveKey('whoFor.options', [
            'yourself' => 'yourself',
            'someoneelse' => 'someoneelse',
            'onlyme' => 'onlyme',
            'onlysomeoneelse' => 'onlysomeoneelse',
        ])
        ->toHaveKey('whoFor.validationRule', 'required|string')
        ->toHaveKey('promoCode.type', 'text')
        ->toHaveKey('promoCode.validationRule', 'nullable|string')
        ->toHaveKey('hideArtwork.type', 'switch')
        ->toHaveKey('hideArtwork.default')
        ->toHaveKey('hideArtwork.validationRule', 'required|boolean')
        ->toHaveKey('hideGroups.type', 'switch')
        ->toHaveKey('hideGroups.default')
        ->toHaveKey('hideGroups.validationRule', 'required|boolean')
        ->toHaveKey('hideUngroupedItems.type', 'switch')
        ->toHaveKey('hideUngroupedItems.default')
        ->toHaveKey('hideUngroupedItems.validationRule', 'required|boolean')
        ->toHaveKey('hideCustomValue.type', 'switch')
        ->toHaveKey('hideCustomValue.default')
        ->toHaveKey('hideCustomValue.validationRule', 'required|boolean')
        ->toHaveKey('customValueAmount.type', 'text')
        ->toHaveKey('customValueAmount.validationRule', 'nullable|string');
});

it('sets page variables in onRun', function(): void {
    Settings::set('company_info', ['id' => 'test-company-id']);
    $page = Page::load('igniter-orange', 'home');
    $pageCode = new PageCode($page, null, controller());
    $component = new GiftUpCheckout($pageCode, [
        'errorPage' => 'error-page-url',
        'successPage' => 'success-page-url',
    ]);

    $component->onRun();

    expect($pageCode['errorPage'])->toBe('http://localhost/error-page-url')
        ->and($pageCode['successPage'])->toBe('http://localhost/success-page-url')
        ->and($pageCode['giftUpOptions'])->toBeInstanceOf(GiftUpOptions::class)
        ->and($pageCode['giftUpOptions']->companyId)->toBe('test-company-id');
});
