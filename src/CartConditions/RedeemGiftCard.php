<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp\CartConditions;

use Exception;
use Igniter\Cart\CartCondition;
use Igniter\Cart\Facades\Cart;
use IgniterLabs\GiftUp\Classes\Manager;
use Override;

class RedeemGiftCard extends CartCondition
{
    public bool $removeable = true;

    public ?int $priority = 200;

    protected $giftCardValue = 0;

    #[Override]
    public function getLabel()
    {
        return lang($this->label).' '.$this->getMetaData('code');
    }

    #[Override]
    public function getValue()
    {
        return 0 - $this->calculatedValue;
    }

    public function getModel() {}

    public function onLoad(): void
    {
        $giftupCode = (string)$this->getMetaData('code');
        if ($giftupCode === '') {
            return;
        }

        try {
            $manager = resolve(Manager::class);

            // Get gift card by code
            $giftCard = $manager->fetchGiftCard($giftupCode);

            $manager->validateGiftCard($giftCard);

            $cartSubtotal = Cart::content()->subtotalWithoutConditions();

            $this->giftCardValue = min($cartSubtotal, $giftCard->remainingValue);
        } catch (Exception $ex) {
            flash()->alert($ex->getMessage())->now();
            $this->removeMetaData('code');
        }
    }

    public function beforeApply()
    {
        return $this->giftCardValue ? null : false;
    }

    #[Override]
    public function getActions()
    {
        $actions = [
            'value' => 0 - $this->giftCardValue,
        ];

        return [$actions];
    }
}
