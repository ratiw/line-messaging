# LINE Messaging for Laravel

Simplify sending LINE messages using Messaging API for Laravel


[![Latest Version on Packagist](https://img.shields.io/packagist/v/ratiw/linemessaging.svg?style=flat-square)](https://packagist.org/packages/ratiw/linemessaging)
[![Total Downloads](https://img.shields.io/packagist/dt/ratiw/linemessaging.svg?style=flat-square)](https://packagist.org/packages/ratiw/linemessaging)


> This package was developed to replace the use LINE Notify
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

Use the `LineMessaging::channel()` to specify the channel access token and **chain** one of the following methods to specify the target user, group, or room.
- `toUser`("USER_ID")
- `toGroup`("GROUP_ID")
- `toRoom`("ROOM_ID")

Then, chain one of the available message types to send the message.

### Sending text message
`text(string $message, $emojis = [], string $quoteToken = null)`
```php
// send a text message to a user
LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toUser('USER_ID')
    ->text('Hello, world!');

// send a text message to a group chat
LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toGroup('GROUP_ID')
    ->text('Hello, world!');
```

### Sending text message with emojis
To include emojis in the text message, you have to specify the emoji placeholders in the text message. 

The placeholder is the `$` character in the message.

Then, you have to includes an array of emoji objects in the second argument.

Each emoji object must have the following keys:
- `productId`: The product ID of the emoji.
- `emojiId`: The emoji ID of the emoji.

```php
LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toUser('USER_ID')
    ->text('$ Happy birthday! $', [
        ['productId' => '5ac2213e040ab15980c9b447', 'emojiId' => '007'],
        ['productId' => '5ac2213e040ab15980c9b447', 'emojiId' => '009'],
    ]);
```

See 
- LINE emojis [here](https://developers.line.biz/en/docs/messaging-api/emoji-list/).
- LINE text message type [here](https://developers.line.biz/en/docs/messaging-api/message-types/#text-messages).
- LINE text message reference [here](https://developers.line.biz/en/reference/messaging-api/#text-message).



### Sending sticker
`sticker(packageId, stickerId)`
```php
LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toGroup('GROUP_ID')
    ->sticker('6359', '11069851');
```

See 
- LINE stickers list [here](https://developers.line.biz/en/docs/messaging-api/sticker-list/).
- LINE sticker message type [here](https://developers.line.biz/en/docs/messaging-api/message-types/#sticker-messages).
- LINE sticker message reference [here](https://developers.line.biz/en/reference/messaging-api/#sticker-message).

### Sending image
`image(string $imageUrl, string $previewUrl = null)`
```php
LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toGroup('GROUP_ID')
    ->image('https://example.com/image.jpg', 'https://example.com/image_preview.jpg');
```
> **Note**
> - `previewUrl` is optional.
> - given URL must be `https` scheme

See 
- LINE images message type [here](https://developers.line.biz/en/docs/messaging-api/message-types/#image-messages).
- LINE image message reference [here](https://developers.line.biz/en/reference/messaging-api/#image-message).

### Sending video
`video(string $videoUrl, string $previewUrl = null, string $trackingId = null)`
```php
LineMessaging::channel('YOUR_CHANNEL_ACCESS_TOKEN')
    ->toUser('USER_ID')
    ->video('https://example.com/video.mp4', 'https://example.com/video_preview.jpg', 'TRACKING_ID');
```
> **Note**
> - `previewUrl` and `trackingId` are optional.
> - given URL must be `https` scheme


## Changelog

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
