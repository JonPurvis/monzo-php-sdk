<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Feed\CreateFeedItemRequest;
use Saloon\Http\BaseResource;

class FeedResource extends BaseResource
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
        return $this->connector->send(new CreateFeedItemRequest(
            accountId: $accountId,
            type: $type,
            params: $params,
            url: $url,
        ));
    }

    /**
     * @param  array<string, string>  $params
     */
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
