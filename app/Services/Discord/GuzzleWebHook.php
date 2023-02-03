<?php

namespace App\Services\Discord;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use App\Services\Discord\Exceptions\InvalidMessage;
use App\Services\Discord\Exceptions\MessageCouldNotBeSent;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleWebHook
{
    protected Client $http;
    protected string $url;

    public function __construct(HttpClient $http, string $url)
    {
        $this->http = $http;
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @throws GuzzleException
     * @throws MessageCouldNotBeSent
     * @throws InvalidMessage
     */
    public function send(Message $message): void
    {
        $payload = $this->buildPayload($message);
        $requestType = $this->requestType($message);

        try {
            $this->http->post($this->url, [$requestType => $payload]);
        } catch (ClientException $e) {
            throw MessageCouldNotBeSent::serviceRespondedWithAnError($e);
        } catch (Exception $e) {
            throw MessageCouldNotBeSent::couldNotCommunicateWithDiscord($e);
        }
    }

    protected function requestType(Message $message): string
    {
        return $message->file !== null ? 'multipart' : 'json';
    }

    /**
     * @throws InvalidMessage
     */
    protected function buildPayload(Message $message): array
    {
        if ($this->isMessageEmpty($message)) {
            throw InvalidMessage::cannotSendAnEmptyMessage();
        }

        if ($this->requestType($message) === 'multipart') {
            return $this->buildMultipartPayload($message);
        }

        return $this->buildJsonPayload($message);
    }

    /**
     * @throws InvalidMessage
     */
    protected function buildJsonPayload(Message $message): array
    {
        if ($this->isMessageEmpty($message)) {
            throw InvalidMessage::cannotSendAnEmptyMessage();
        }

        return collect($message->toArray())->forget('file')->all();
    }

    /**
     * @throws InvalidMessage
     */
    protected function buildMultipartPayload(Message $message): array
    {
        if ($message->embeds !== null) {
            throw InvalidMessage::embedsNotSupportedWithFileUploads();
        }

        return collect($message->toArray())
            ->forget('file')
            ->reject(static function ($value) {
                return $value === null;
            })
            ->map(static function ($value, $key) {
                return ['name' => $key, 'contents' => $value];
            })
            ->push($message->file)
            ->values()
            ->all();
    }

    protected function isMessageEmpty($message): bool
    {
        return $message->content === null
               && $message->file === null
               && $message->embeds === null;
    }
}
