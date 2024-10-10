<?php

namespace Ratiw\LineMessaging;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class LineMessaging
{
    protected Http $http;
    protected string $channelToken;
    protected string $source;
    protected string $userId;
    protected string $groupId;
    protected string $roomId;
    protected string $type;
    protected ?string $retryKey = null;

    protected const API_URL = 'https://api.line.me/v2/bot/message/push';

    public function __construct(string $channelToken) {
        $this->channelToken = $channelToken;
    }

    public static function channel($channelToken): self
    {
        return new static($channelToken);
    }

    public function toUser(string $userId): self
    {
        $this->userId = $userId;
        $this->source = 'user';

        return $this;
    }

    public function toGroup(string $groupId): self
    {
        $this->groupId = $groupId;
        $this->source = 'group';

        return $this;
    }

    public function toRoom(string $roomId): self
    {
        $this->roomId = $roomId;
        $this->source = 'room';

        return $this;
    }

    public function withRetryKey(string $retryKey): self
    {
        $this->retryKey = $retryKey;

        return $this;
    }

    public function getSource(): string
    {
        return match ($this->source) {
            'user' => $this->userId,
            'group' => $this->groupId,
            'room' => $this->roomId,
            default => throw new \Exception('Invalid source'),
        };
    }

    protected function apiPost(array $data): Response
    {
        return Http::withHeaders(
            collect([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->channelToken,
            ])->when(!empty($this->retryKey), function ($collection){
                $collection->put('X-Line-Retry-Key', $this->retryKey);
            })->toarray()
        )->post(self::API_URL, [
            'to' => $this->getSource(),
            'messages' => $data,
        ]);
    }

    public function text(string $message, array $emojis = [], string $quoteToken = null): Response
    {
        $this->type = 'text';
        $indexes = $this->findStrPosAll($message, '$');
        if (count($emojis) === count($indexes)) {
            $emojis = collect($emojis)->map(function ($collection, $pos) use($indexes) {
                return [
                    'index' => $indexes[$pos],
                    'productId' => $collection['productId'],
                    'emojiId' => $collection['emojiId'],
                ];
            });
        }

        $message = collect([
            'type' => $this->type,
            'text' => $message,
        ])->when($quoteToken, function ($collection) use ($quoteToken) {
            $collection->put('quoteToken', $quoteToken);
        })->when(count($emojis), function ($collection) use ($emojis) {
            $collection->put('emojis', $emojis);
        })->toArray();

        return $this->apiPost([$message]);
    }

    private function findStrPosAll(string $haystack, string $needle): array
    {
        $pos = [];
        $index = 0;
        while ($index < strlen($haystack)) {
            if ($haystack[$index] === $needle) {
                $pos[] = $index;
            }
            $index++;
        }

        return $pos;
    }

    public function image(string $imageUrl, string $previewUrl = null): Response
    {
        $this->type = 'image';
        $messages = [
            [
                'type' => $this->type,
                'originalContentUrl' => $imageUrl,
                'previewImageUrl' => $previewUrl ?? $imageUrl,
            ],
        ];

        return $this->apiPost($messages);
    }

    public function sticker($packageId, $stickerId): Response
    {
        $this->type = 'sticker';
        $messages = [
            [
                'type' => $this->type,
                'packageId' => $packageId,
                'stickerId' => $stickerId,
            ],
        ];

        return $this->apiPost($messages);
    }

    public function video(string $videoUrl, string $previewUrl = null, string $trackingId = null): Response
    {
        $this->type = 'video';
        $message = collect([
            'type' => $this->type,
            'originalContentUrl' => $videoUrl,
            'previewImageUrl' => $previewUrl ?? $videoUrl,
        ])->when($trackingId, fn ($collection) => $collection->put('trackingId', $trackingId))
        ->toArray();

        return $this->apiPost([$message]);
    }

    public function audio(string $audioUrl, int $duration): Response
    {
        $this->type = 'audio';
        $message = collect([
            'type' => $this->type,
            'originalContentUrl' => $audioUrl,
            'duration' => $duration,
        ])->toArray();

        return $this->apiPost([$message]);
    }

    public function location(string $title, string $address, float $latitude, float $longitude): Response
    {
        $this->type = 'location';
        $message = collect([
            'type' => $this->type,
            'title' => $title,
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ])->toArray();

        return $this->apiPost([$message]);
    }
}
