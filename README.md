# LINE Messaging for Laravel

Simplify sending LINE messages using Messaging API for Laravel


[![Latest Version on Packagist](https://img.shields.io/packagist/v/ratiw/linemessaging.svg?style=flat-square)](https://packagist.org/packages/ratiw/linemessaging)
[![Total Downloads](https://img.shields.io/packagist/dt/ratiw/linemessaging.svg?style=flat-square)](https://packagist.org/packages/ratiw/linemessaging)


> This package was developed to replace the use LINE Notify.
> as it will be discontinued on March 31, 2025.

## Basic Usage

```php
use Ratiw\LineMessaging\LineMessaging;
use Illuminate\Http\Client\Response;

// simple text message to a user
$response = LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toUser('USER_ID')
    ->text('Hi, this is a test message.');

// sticker message to a group chat
$response = LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toGroup('GROUP_ID')
    ->sticker('6359', '11069851');

// text with emojis
$response = LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toGroup('GROUP_ID')
    ->text('Hey, $ LINE emoji $', [
        ['productId' => '5ac2213e040ab15980c9b447', 'emojiId' => '009'],
        ['productId' => '5ac21d59031a6752fb806d5d', 'emojiId' => '004'],
    ]);
```

## Understanding LINE Messaging API

In order to send LINE messages via LINE Messaging API, you need the following:
- Channel access token
- User ID, Group ID, or Room ID
- what type of message you want to send

Getting those information can be tricky, but you usually do it once and then you can reuse it. 

If you do not know how to get those information, please refer to the [LINE Messaging API](https://developers.line.biz/en/docs/messaging-api/overview/) documentation, Or search for video tutorials on YouTube.

### Getting User ID, Group ID, or Room ID

You can definitely use a free [webhook.site](https://webhook.site) to gather user IDs with the LINE Messaging API, but there's a key point to remember:  The user ID will only be included in the webhook data if the user has consented to share their profile information with your LINE Official Account.

That means the user has to add your LINE Official Account as a friend (and, you'll get the user ID from the webhook), or invite your LINE Official Account to a group chat (and, you'll get the group ID from the webhook).

### Message Types 

This package currently supports the following message types:
- Text
- Sticker
- Image
- Video

More might be added in the future, but no promises here. :smiley:


## Installation

You can install the package via composer:

```bash
composer require ratiw/line-messaging
```

## Usage

```php
// Usage description here
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Rati Wannapanop](https://github.com/ratiw)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
