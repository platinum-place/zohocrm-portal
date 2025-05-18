<?php

namespace Zoho\Api;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class CRM
{
    protected function getApiUrl(): string
    {
        return config('zoho.domains.api').'/'.config('zoho.crm.uri');
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getRecords(string $module, string $token, array $fields, ?string $id = ''): ?array
    {
        $url = sprintf('%s/%s%s',
            $this->getApiUrl(),
            $module,
            $id ? "/$id" : ''
        );

        return Http::withToken($token, 'Zoho-oauthtoken')
            ->get($url, [
                'fields' => implode(',', $fields),
            ])
            ->throw()
            ->json();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function searchRecords(string $module, string $token, string $criteria): ?array
    {
        $url = sprintf('%s/%s/search',
            $this->getApiUrl(),
            $module,
        );

        return Http::withToken($token, 'Zoho-oauthtoken')
            ->get($url, http_build_query(['criteria' => $criteria]))
            ->throw()
            ->json();
    }
}
