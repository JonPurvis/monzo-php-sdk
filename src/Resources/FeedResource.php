<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use InvalidArgumentException;
use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Feed\CreateFeedItemRequest;

class FeedResource extends MonzoResource
{
    /**
     * @param  array<string, string>  $params
     */
    public function create(
        string $accountId,
        string $type,
        array $params,
        ?string $url = null,
    ): MonzoResponse {
        if ($params === []) {
            throw new InvalidArgumentException('Feed item params are required.');
        }

        return $this->sendMonzoRequest(new CreateFeedItemRequest(
            accountId: $accountId,
            type: $type,
            params: $params,
            url: $url,
        ));
    }

    public function createBasic(
        string $accountId,
        string $title,
        string $imageUrl,
        ?string $url = null,
        ?string $body = null,
        ?string $backgroundColor = null,
        ?string $titleColor = null,
        ?string $bodyColor = null,
    ): MonzoResponse {
        if ($title === '' || $imageUrl === '') {
            throw new InvalidArgumentException('A basic feed item requires title and image_url.');
        }

        foreach ([$backgroundColor, $titleColor, $bodyColor] as $color) {
            if ($color !== null && preg_match('/^#[0-9A-Fa-f]{6}$/', $color) !== 1) {
                throw new InvalidArgumentException('Feed item colours must be hex in the form #RRGGBB.');
            }
        }

        $params = array_filter([
            'title' => $title,
            'image_url' => $imageUrl,
            'body' => $body,
            'background_color' => $backgroundColor,
            'title_color' => $titleColor,
            'body_color' => $bodyColor,
        ], fn (mixed $value): bool => $value !== null && $value !== '');

        return $this->create(
            accountId: $accountId,
            type: 'basic',
            params: $params,
            url: $url,
        );
    }
}
