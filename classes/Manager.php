<?php

namespace IgniterLabs\GiftUp\Classes;

use Admin\Models\Orders_model;
use Exception;
use Igniter\Flame\Cart\Facades\Cart;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Flame\Traits\Singleton;
use IgniterLabs\GiftUp\Models\Settings;
use Illuminate\Support\Facades\Http;

class Manager
{
    use Singleton;

    public $endpoint = 'https://api.giftup.app/';

    protected static $responseCache;

    public function applyGiftCardCode(string $code)
    {
        if (!$condition = Cart::getCondition('giftup'))
            return;

        // Get gift card by code
        $giftCardObj = $this->fetchGiftCard($code);

        $this->validateGiftCard($giftCardObj);

        $condition->setMetaData(['code' => $code]);

        Cart::loadCondition($condition);

        return $condition;
    }

    public function validateGiftCard($giftCard)
    {
        if ($giftCard->backingType !== 'Currency')
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_invalid_type'));

        if ($giftCard->hasExpired)
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_expired'));

        if ($giftCard->notYetValid)
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_invalid'));

        if (!$giftCard->canBeRedeemed)
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_redeemed'));

        if ($giftCard->remainingValue <= Settings::getMinimumValue())
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_gift_card_balance_low'));
    }

    public function redeemGiftCard(Orders_model $order)
    {
        if (!Settings::isConnected())
            return;

        if (!$condition = Cart::conditions()->get('giftup'))
            return;

        if (!strlen($condition->getMetaData('code')))
            return;

        if (!$order->isPaymentProcessed())
            throw new ApplicationException(lang('igniterlabs.giftup::default.alert_order_not_processed'));

        $payload = [
            'amount' => abs($condition->getValue()),
            'units' => null,
            'reason' => sprintf(lang('igniterlabs.giftup::default.text_giftup_reason'), $order->order_id),
            'locationId' => null,
            'metadata' => [
                "ExternalOrderId" => $order->order_id,
            ],
        ];

        $uri = sprintf('gift-cards/%s/redeem', $condition->getMetaData('code'));
        $response = $this->sendRequest('POST', $uri, $payload);

        $order->logPaymentAttempt('Gift card redeemed successful', 1, $payload, $response->json());
    }

    public function fetchCompany()
    {
        return $this->sendRequest('GET', 'company')->json();
    }

    public function listLocations()
    {
        return $this->sendRequest('GET', 'locations')->json();
    }

    public function fetchGiftCard(string $code)
    {
        if (array_key_exists($code, self::$responseCache))
            return self::$responseCache[$code];

        return self::$responseCache[$code] = $this->sendRequest('GET', 'gift-cards/'.$code)->json();
    }

    protected function sendRequest($method, $uri, array $payload = [])
    {
        try {
            if (!strlen($apiKey = Settings::getApiKey()))
                throw new Exception('Please connect your Gift Up! account to TastyIgniter in Settings > Gift Up!');

            $headers = [
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ];

            if (Settings::isStaging())
                $headers['x-giftup-testmode'] = 'true';

            return Http::withHeaders($headers)->send($method, $this->endpoint.'/'.$uri, $payload);
        }
        catch (Exception $ex) {
            log_message('error', $ex);
        }
    }
}
