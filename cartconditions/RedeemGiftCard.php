<?php

namespace IgniterLabs\GiftUp\CartConditions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Igniter\Flame\Cart\Facades\Cart;
use Igniter\Flame\Cart\CartCondition;
use IgniterLabs\GiftUp\Classes\Manager;

class RedeemGiftCard extends CartCondition
{
    public $removeable = true;

    public $priority = 200;

    protected $giftCardValue = 0;

    public $conditions = null;

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

            $cartSubtotal = $this->calculateGiftUpValue();

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

    private function calculateGiftUpValue(): float
    {
        $this->conditions = Cart::conditions();

        $cartConditionsTobeAppliedOnGiftup = $this->getConditionsBeforeGiftUp();

        $cartSubtotal = Cart::content()->subtotalWithoutConditions();

        $this->conditions->map(function($cartCondition) use ($cartConditionsTobeAppliedOnGiftup, &$cartSubtotal){

            $filteredCartCondition = $cartConditionsTobeAppliedOnGiftup->first(function($giftupCondition) use ($cartCondition){
                return $giftupCondition['name'] === $cartCondition->name;
            });

            if ($filteredCartCondition) {
                $cartSubtotal += $cartCondition->calculatedValue;
            }
        });

        return $cartSubtotal;
    }

    private function getConditionsBeforeGiftUp(): Collection
    {
        $igniterCartSettings = DB::table('extension_settings')
            ->where('item', 'igniter_cart_settings')
            ->first();

        $data = json_decode($igniterCartSettings->data, true);
        $conditions = collect($data['conditions']);
        $giftupPriority = $conditions->get('giftup')['priority'];

        // Filter conditions with a priority less than 'giftup'
        $filteredConditions = $conditions->filter(function ($condition) use ($giftupPriority) {
            return $condition['priority'] < $giftupPriority;
        });

        // Sort the conditions by priority in ascending order
        $sortedConditions = $filteredConditions->sortBy('priority');

        // Return the sorted collection
        return $sortedConditions->values();  // Use values() to reindex the collection
    }
}
