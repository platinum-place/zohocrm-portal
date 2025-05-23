<?php

namespace App\Services;

use Exception;
use ZohoCRM;

class ZohoCRMService
{
    public function __construct(protected ZohoOAuthService $oauth) {}

    public function searchRecords(string $module, string $criteria, ?int $page = 1, ?int $perPage = 200): ?array
    {
        $token = $this->oauth->getAccessToken();

        $response = ZohoCRM::searchRecords($module, $token, $criteria, $page, $perPage);

        if (empty($response)) {
            throw new Exception(__('Not Found'));
        }

        return $response;
    }

    public function getRecords(string $module, array $fields, ?string $id = ''): ?array
    {
        $token = $this->oauth->getAccessToken();

        $response = ZohoCRM::getRecords($module, $token, $fields, $id);

        if (empty($response)) {
            throw new Exception(__('Not Found'));
        }

        return $response;
    }

    public function updateRecords(string $module, string $id, array $data): ?array
    {
        $token = $this->oauth->getAccessToken();

        $response = ZohoCRM::updateRecords($module, $token, $id, $data);

        if (empty($response)) {
            throw new Exception(__('Not Found'));
        }

        return $response;
    }

    public function uploadAnAttachment(string $module, string $id, string $filePath): void
    {
        $token = $this->oauth->getAccessToken();

        ZohoCRM::uploadAttachment($module, $token, $id, $filePath);
    }

    public function attachmentList(string $module, string $id, array $fields): ?array
    {
        $token = $this->oauth->getAccessToken();

        $response = ZohoCRM::attachmentList($module, $token, $id, $fields);

        if (empty($response)) {
            throw new Exception(__('Not Found'));
        }

        return $response;
    }

    public function getAttachment(string $module, string $id, string $attachmentId): ?string
    {
        $token = $this->oauth->getAccessToken();

        $response = ZohoCRM::getAttachment($module, $token, $id, $attachmentId);

        if (empty($response)) {
            throw new Exception(__('Not Found'));
        }

        return $response;
    }

    public function insertRecords(string $module, array $data): ?array
    {
        $token = $this->oauth->getAccessToken();

        return ZohoCRM::insertRecords($module, $token, $data);
    }
}
