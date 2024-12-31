<p align="center">
    <a href="https://github.com/igniter-labs/ti-ext-giftup/actions"><img src="https://github.com/igniter-labs/ti-ext-giftup/actions/workflows/pipeline.yml/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/igniterlabs/ti-ext-giftup"><img src="https://img.shields.io/packagist/dt/igniterlabs/ti-ext-giftup" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/igniterlabs/ti-ext-giftup"><img src="https://img.shields.io/packagist/v/igniterlabs/ti-ext-giftup" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/igniterlabs/ti-ext-giftup"><img src="https://img.shields.io/github/license/igniter-labs/ti-ext-giftup" alt="License"></a>
</p>

## Introduction

With [Gift Up!](https://www.giftup.com/), you can sell and accept your own gift cards on your TastyIgniter website. It's
completely free to get started, and there are no monthly fees; you only pay a small fee when you sell a gift card.

Gift Up! makes it easy for your customers to buy gift cards from your website and social media platforms at any time,
with your branding. Customers can pay with debit or credit cards, as well as alternative payment options such as Apple
Pay and Google Pay.

Gift Cards can be applied during TastyIgniter checkout.

## Features

- Customized gift cards with your branding
- Simple Gift Up! checkout experience on any TastyIgniter page
- Gift Cards delivered automatically through email
- Accept the gift cards you sell anywhere, including in-store, through Gift Up! mobile apps, or during checkout.
- A complete Gift Up! management dashboard.

## Installation

You can install the extension via composer using the following command:

```bash
composer require igniterlabs/ti-ext-giftup:"^4.0" -W
```

## Getting started

- Go to **Manage > Settings > Gift Up! Settings
  ** to enter your API Key ([obtained from your Gift Up! account](https://giftup.com))
- Enable the Gift Up! Cart Condition under by navigating to the _Manage > Settings > Cart Settings_ admin settings page
- Switch to enable the `giftup` cart condition
- Add the `Gift Up!` component to any page on your website. This will render Gift Up! checkout enabling your customers to buy your gift cards.

## Usage

Customers can redeem gift cards during checkout by entering the gift card code in the checkout page. The gift card amount will be deducted from the order total. If the gift card amount is greater than the order total, the remaining balance will be stored for future use. If the gift card amount is less than the order total, the customer will be prompted to pay the remaining balance using other payment methods.

## Changelog

Please see [CHANGELOG](https://github.com/igniter-labs/ti-ext-giftup/blob/master/CHANGELOG.md) for more information on what has changed recently.

## Reporting issues

If you encounter a bug in this extension, please report it using the [Issue Tracker](https://github.com/igniter-labs/ti-ext-giftup/issues) on GitHub.

## Contributing

Contributions are welcome! Please read [TastyIgniter's contributing guide](https://tastyigniter.com/docs/contribution-guide).

## Security vulnerabilities

For reporting security vulnerabilities, please see our [our security policy](https://github.com/igniter-labs/ti-ext-giftup/security/policy).

## License

TastyIgniter User extension is open-source software licensed under the [MIT license](https://github.com/igniter-labs/ti-ext-giftup/blob/master/LICENSE.md).
