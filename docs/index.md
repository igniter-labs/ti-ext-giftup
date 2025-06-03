---
title: "Gift Up!"
section: "extensions"
sortOrder: 999
---

## Installation

You can install the extension via composer using the following command:

```bash
composer require igniterlabs/ti-ext-giftup -W
```

## Getting started

- Go to **Manage > Settings > Gift Up! Settings** to enter your API Key ([obtained from your Gift Up! account](https://giftup.com))
- Enable the Gift Up! Cart Condition under by navigating to the _Manage > Settings > Cart Settings_ admin settings page
- Switch to enable the `giftup` cart condition
- Add the `Gift Up!` component to any page on your website. This will render Gift Up! checkout enabling your customers to buy your gift cards.

## Usage

Customers can redeem gift cards during checkout by entering the gift card code in the checkout page. The gift card amount will be deducted from the order total. If the gift card amount is greater than the order total, the remaining balance will be stored for future use. If the gift card amount is less than the order total, the customer will be prompted to pay the remaining balance using other payment methods.
