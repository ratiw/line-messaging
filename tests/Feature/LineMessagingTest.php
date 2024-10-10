<?php

namespace Ratiw\LineMessaging\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Ratiw\LineMessaging\LineMessaging;
use Ratiw\LineMessaging\Tests\TestCase;

class LineMessagingTest extends TestCase
{
    public function test_sending_message_has_required_headers(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->text('Hello World');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Content-Type', 'application/json')
                && $request->hasHeader('Authorization', 'Bearer DUMMY_CHANNEL')
                && $request->url() === LineMessaging::getApiUrl()
                ;
        });
    }

    public function test_sending_text_message_to_user(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->text('Hello World');

        Http::assertSent(function ($request): bool {
            return $request['to'] === 'DUMMY_USER'
                && $request['messages'][0]['type'] === 'text'
                && $request['messages'][0]['text'] === 'Hello World'
                ;
        });
    }

    public function test_sending_text_message_to_group(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_GROUP')
            ->text('Hello World');

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_GROUP'
                && $request['messages'][0]['type'] === 'text'
                && $request['messages'][0]['text'] === 'Hello World'
                ;
        });
    }

    public function test_sending_text_message_with_reply_quote(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_GROUP')
            ->text('Hello World', [], 'DUMMY_QUOTE_TOKEN');

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_GROUP'
                && $request['messages'][0]['type'] === 'text'
                && $request['messages'][0]['text'] === 'Hello World'
                && $request['messages'][0]['quoteToken'] === 'DUMMY_QUOTE_TOKEN'
                ;
        });
    }
    
    public function test_sending_text_message_with_emojis_to_user(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_GROUP')
            ->text('Hello $ emojis $', [
                ['productId' => 'DUMMY_PRODUCT_ID', 'emojiId' => 'ID_001'],
                ['productId' => 'DUMMY_PRODUCT_ID', 'emojiId' => 'ID_002'],
            ]);

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_GROUP'
                && $request['messages'][0]['type'] === 'text'
                && $request['messages'][0]['text'] === 'Hello $ emojis $'
                && $this->arrayIsSame($request['messages'][0]['emojis'][0], [
                    'index' => 6,
                    'productId' => 'DUMMY_PRODUCT_ID',
                    'emojiId' => 'ID_001',
                ])
                && $this->arrayIsSame($request['messages'][0]['emojis'][1], [
                    'index' => 15,
                    'productId' => 'DUMMY_PRODUCT_ID',
                    'emojiId' => 'ID_002',
                ])
                ;
        });
    }

    private function arrayIsSame(array $array1, array $array2): bool
    {
        return count(array_diff_assoc($array1, $array2)) === 0;
    }

    public function test_sending_image_message_to_user(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->image('https://example.com/image.jpg', 'https://example.com/image_preview.jpg');

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_USER'
                && $request['messages'][0]['type'] === 'image'
                && $request['messages'][0]['originalContentUrl'] === 'https://example.com/image.jpg'
                && $request['messages'][0]['previewImageUrl'] === 'https://example.com/image_preview.jpg'
                ;
        });
    }

    public function test_sending_image_message_without_preview(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->image('https://example.com/image.jpg');

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_USER'
                && $request['messages'][0]['type'] === 'image'
                && $request['messages'][0]['originalContentUrl'] === 'https://example.com/image.jpg'
                && $request['messages'][0]['previewImageUrl'] === 'https://example.com/image.jpg'
                ;
        });
    }

    public function test_sending_sticker_message_to_user(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->sticker('DUMMY_PACKAGE_ID', 'DUMMY_STICKER_ID');

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_USER'
                && $request['messages'][0]['type'] === 'sticker'
                && $request['messages'][0]['packageId'] === 'DUMMY_PACKAGE_ID'
                && $request['messages'][0]['stickerId'] === 'DUMMY_STICKER_ID'
                ;
        });
    }

    public function test_sending_video_message_to_user(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->video('https://example.com/video.mp4', 'https://example.com/video_preview.jpg');

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_USER'
                && $request['messages'][0]['type'] === 'video'
                && $request['messages'][0]['originalContentUrl'] === 'https://example.com/video.mp4'
                && $request['messages'][0]['previewImageUrl'] === 'https://example.com/video_preview.jpg'
                ;
        });
    }

    public function test_sending_video_message_to_user_with_tracking_id(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->video(
                'https://example.com/video.mp4', 
                'https://example.com/video_preview.jpg',
                'DUMMY_TRACKING_ID'
            );

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_USER'
                && $request['messages'][0]['type'] === 'video'
                && $request['messages'][0]['originalContentUrl'] === 'https://example.com/video.mp4'
                && $request['messages'][0]['previewImageUrl'] === 'https://example.com/video_preview.jpg'
                && $request['messages'][0]['trackingId'] === 'DUMMY_TRACKING_ID'
                ;
        });
    }

    public function test_sending_audio_message_to_user(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->audio('https://example.com/audio.mp3', 3000);

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_USER'
                && $request['messages'][0]['type'] === 'audio'
                && $request['messages'][0]['originalContentUrl'] === 'https://example.com/audio.mp3'
                && $request['messages'][0]['duration'] === 3000
                ;
        });
    }

    public function test_sending_location_message_to_user(): void
    {
        Http::fake();

        LineMessaging::channel('DUMMY_CHANNEL')
            ->toUser('DUMMY_USER')
            ->location('DUMMY_TITLE', 'DUMMY_ADDRESS', 13.736717, 100.523186);

        Http::assertSent(function ($request) {
            return $request['to'] === 'DUMMY_USER'
                && $request['messages'][0]['type'] === 'location'
                && $request['messages'][0]['title'] === 'DUMMY_TITLE'
                && $request['messages'][0]['address'] === 'DUMMY_ADDRESS'
                && $request['messages'][0]['latitude'] === 13.736717
                && $request['messages'][0]['longitude'] === 100.523186
                ;
        });
    }
}