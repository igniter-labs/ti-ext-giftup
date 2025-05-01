<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp\Classes;

use Exception;
use Igniter\Cart\Facades\Cart;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Exception\ApplicationException;
use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Manager
{
    public $endpoint = 'https://api.giftup.app/';

    protected static $responseCache = [];

    public function applyGiftCardCode(string $code)
    {
        try {
            if (!$condition = Cart::getCondition('giftup')) {
                return null;
            }

            // Get gift card by code
            $giftCardObj = $this->fetchGiftCard($code);

            $this->validateGiftCard($giftCardObj);

            $condition->setMetaData(['code' => $code]);

            Cart::loadCondition($condition);

            return $condition;
        } catch (Exception $ex) {
            Log::error($ex);
        }

        return null;
    }

    public function validateGiftCard($giftCard): void
    {
        if ($giftCard->backingType !== 'Currency') {
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_invalid_type'));
        }

        if ($giftCard->hasExpired) {
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_expired'));
        }

        if ($giftCard->notYetValid) {
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_invalid'));
        }

        if (!$giftCard->canBeRedeemed) {
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_redeemed'));
        }

        if ($giftCard->remainingValue <= Settings::getMinimumValue()) {
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_balance_low'));
        }
    }

    public function redeemGiftCard(Order $order): void
    {
        if (!$condition = Cart::conditions()->get('giftup')) {
            return;
        }

        if ((string)$condition->getMetaData('code') === '') {
            return;
        }

        if ($order->isPaymentProcessed()) {
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_order_not_processed'));
        }

        $payload = [
            'amount' => abs($condition->getValue()),
            'units' => null,
            'reason' => sprintf(lang('igniterlabs.giftup::default.text_giftup_reason'), $order->order_id),
            'locationId' => null,
            'metadata' => [
                'ExternalOrderId' => $order->order_id,
            ],
        ];

        $uri = sprintf('gift-cards/%s/redeem', $condition->getMetaData('code'));
        $response = $this->sendRequest('POST', $uri, [
            'body' => json_encode($payload),
        ]);

        if ($order->payment_method) {
            $order->logPaymentAttempt('Gift card redeemed successful', 1, $payload, $response);
        }
    }

    public function fetchCompany()
    {
        return $this->sendRequest('GET', 'company');
    }

    public function fetchGiftCard(string $code)
    {
        if (array_key_exists($code, self::$responseCache)) {
            return self::$responseCache[$code];
        }

        return self::$responseCache[$code] = (object)$this->sendRequest('GET', 'gift-cards/'.$code);
    }

    public function clearInternalCache(): void
    {
        self::$responseCache = [];
    }

    protected function sendRequest(string $method, string $uri, array $payload = [])
    {
        try {
            $apiKey = (string)Settings::getApiKey();
            if ($apiKey === '') {
                throw new Exception(lang('igniterlabs.giftup::default.alert_missing_api_key'));
            }

            $headers = [
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ];

            if (Settings::isStaging()) {
                $headers['x-giftup-testmode'] = true;
            }

            $request = Http::withHeaders($headers)->send($method, $this->endpoint.'/'.$uri, $payload);

            if (!$request->ok()) {
                throw new ApplicationException('Error while communicating with the giftup server '.json_encode($request->json()));
            }

            return $request->json();
        } catch (Exception $ex) {
            logger()->error($ex);

            throw $ex;
        }
    }
}
