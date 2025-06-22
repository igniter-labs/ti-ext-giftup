<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp\Tests\Models;

use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Http;

afterEach(function(): void {
    Settings::clearInternalCache();
});

it('returns true when isConnected is configured and companyId exists', function(): void {
    Settings::set('api_key', 'old-test-key');
    Settings::set('company_info', ['id' => '12345']);

    expect(Settings::isConnected())->toBeTrue();
});

it('returns false when isConnected is not configured', function(): void {
    $settings = new Settings;
    $settings->set('is_configured', false);

    expect($settings->isConnected())->toBeFalse();
});

it('returns the correct API key', function(): void {
    $settings = new Settings;
    $settings->set('api_key', 'test_api_key');

    expect($settings->getApiKey())->toBe('test_api_key');
});

it('returns true when isStaging is set to staging', function(): void {
    $settings = new Settings;
    $settings->set('is_live', 'staging');

    expect($settings->isStaging())->toBeTrue();
});

it('returns the correct minimum value as an integer', function(): void {
    $settings = new Settings;
    $settings->set('minimum_value', '50');

    expect($settings->getMinimumValue())->toBe(50);
});

it('returns the correct company ID', function(): void {
    $settings = new Settings;
    $settings->set('company_info', ['id' => '12345']);

    expect($settings->getCompanyId())->toBe('12345');
});

it('updates company info after save', function(): void {
    Settings::flushEventListeners();
    Http::fake([
        'https://api.giftup.app/*' => Http::response([
            'id' => 'test-company-id',
        ]),
    ]);
    Settings::set('api_key', 'test-key');

    expect(Settings::get('company_info'))->toBe(['id' => 'test-company-id']);
});
