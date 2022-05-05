<?php

namespace IgniterLabs\GiftUp\CartConditions;

use Igniter\Flame\Cart\CartCondition;
use Igniter\Flame\Cart\Facades\Cart;
use IgniterLabs\GiftUp\Classes\Manager;

class RedeemGiftCard extends CartCondition
{
    public $removeable = true;

    public $priority = 200;

    protected $giftCardValue = 0;

    public function getLabel()
    {
        return lang($this->label).' '.$this->getMetaData('code');
    }

    public function getValue()
    {
        return 0 - $this->calculatedValue;
    }

    public function getModel()
    {
    }

    public function onLoad()
    {
        if (!strlen($giftupCode = $this->getMetaData('code')))
            return;

        try {
            $manager = Manager::instance();

            // Get gift card by code
            $giftCard = $manager->fetchGiftCard($giftupCode);

            $manager->validateGiftCard($giftCard);

            $cartSubtotal = Cart::content()->subtotalWithoutConditions();

            $this->giftCardValue = $cartSubtotal > $giftCard->remainingValue
                ? $giftCard->remainingValue : $cartSubtotal;
        }
        catch (\Exception $ex) {
            flash()->alert($ex->getMessage())->now();
            $this->removeMetaData('code');
        }
    }

    public function beforeApply()
    {
        if (!$this->giftCardValue)
            return false;
    }

    public function getActions()
    {
        $actions = [
            'value' => 0 - $this->giftCardValue,
        ];

        return [$actions];
    }
}
