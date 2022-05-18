<?php

namespace IgniterLabs\GiftUp\Classes;

class GiftUpOptions
{
    public $companyId;

    public $domain;

    public $product;

    public $group;

    public $language;

    public $purchaserName;

    public $purchaserEmail;

    public $recipientName;

    public $recipientEmail;

    public $step;

    public $whoFor;

    public $promoCode;

    public $hideArtwork;

    public $hideGroups;

    public $hideUngroupedItems;

    public $hideCustomValue;

    public $customValueAmount;

    public function __construct(array $options)
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key))
                $this->$key = $value;
        }
    }
}
