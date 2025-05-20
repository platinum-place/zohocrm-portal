<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class QuoteService extends ZohoCRMService
{
    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function get(string $id): ?array
    {
        $fields = ['id'];

        return $this->getRecords('Quotes', $fields, $id);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function update(string $id, array $data): ?array
    {
        return $this->updateRecords('Quotes', $id, $data);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function uploadAttachment(string $id, string $filePath): void
    {
        $this->uploadAnAttachment('Quotes', $id, $filePath);
    }

    public function getAttachments(string $id): ?array
    {
        $fields = ['id','File_Name'];

        return $this->attachmentList('Quotes', $id,$fields);
    }
}
