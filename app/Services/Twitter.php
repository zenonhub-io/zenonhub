<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;

class Twitter
{
    private TwitterOAuth $twitter;

    public function __construct(
        private string $apiKey,
        private string $apiKeySecret,
        private string $accessToken,
        private string $accessTokenSecret,
    ) {
        $this->twitter = new TwitterOAuth(
            $this->apiKey,
            $this->apiKeySecret,
            $this->accessToken,
            $this->accessTokenSecret
        );
    }

    public function tweet(string $status): bool
    {
        $result = $this->twitter->post('statuses/update', ['status' => $status]);

        if ($this->twitter->getLastHttpCode() == 200) {
            return true;
        }

        Log::warning('Unable to send tweet - '.$result->errors[0]->message);

        return false;
    }
}
