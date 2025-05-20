<?php

namespace App\Services;

use App\Models\Zoho\ZohoOauthAccessToken;
use App\Models\Zoho\ZohoOauthRefreshToken;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use Throwable;
use ZohoCRM;
use ZohoOAuth;

class ZohoCRMService extends ZohoService
{
    /**
     * @throws Throwable
     * @throws ConnectionException
     * @throws RequestException
     */
    public function searchRecords(string $module, string $criteria, ?int $page = 1, ?int $perPage = 200): ?array
    {
        $token = $this->getAccessToken();

        $response = ZohoCRM::searchRecords($module, $token, $criteria, $page, $perPage);

        if (empty($response)) {
            throw new Exception(__('Not Found'));
        }

        return $response;
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     * @throws RequestException
     */
    public function getRecords(string $module, array $fields, ?string $id = ''): ?array
    {
        $token = $this->getAccessToken();

        $response = ZohoCRM::getRecords($module, $token, $fields, $id);

        if (empty($response)) {
            throw new Exception(__('Not Found'));
        }

        return $response['data'][0];
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function updateRecords(string $module, string $id, array $data): ?array
    {
        $token = $this->getAccessToken();

        $response = ZohoCRM::updateRecords($module, $token, $id, $data);

        if (empty($response)) {
            throw new Exception(__('Not Found'));
        }

        return $response;
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     * @throws RequestException
     */
    public function uploadAnAttachment(string $module, string $id, string $filePath): void
    {
        $token = $this->getAccessToken();

        ZohoCRM::uploadAttachment($module, $token, $id, $filePath);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function attachmentList(string $module, string $id, array $fields): ?array
    {
        $token = $this->getAccessToken();

        return ZohoCRM::attachmentList($module, $token, $id, $fields);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function getAttachment(string $module, string $id, string $attachmentId): ?array
    {
        $token = $this->getAccessToken();

        return ZohoCRM::getAttachment($module, $token, $id, $attachmentId);
    }
}
