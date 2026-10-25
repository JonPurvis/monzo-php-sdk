<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Feed;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class CreateFeedItemRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<string, string>  $params
     */
    public function __construct(
        protected string $accountId,
        protected string $type,
        protected array $params,
        protected ?string $url = null,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/feed';
    }

    protected function defaultBody(): array
    {
        $body = [
            'account_id' => $this->accountId,
            'type' => $this->type,
        ];

        if ($this->url !== null) {
            $body['url'] = $this->url;
        }

        foreach ($this->params as $key => $value) {
            $body['params['.$key.']'] = $value;
        }

        return $body;
    }
}
