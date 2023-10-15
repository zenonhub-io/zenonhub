<?php

namespace App\Services;

use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Support\Facades\App;

class Zenon
{
    public static function verifySignature(string $publicKey, string $address, string $message, string $signature): bool
    {
        $validated = false;
        $validSignature = ZnnUtilities::verifySignedMessage(
            $publicKey,
            $message,
            $signature
        );

        $account = Account::findByAddress($address);
        $accountCheck = ZnnUtilities::addressFromPublicKey($publicKey);

        if ($validSignature && ($address === $accountCheck)) {
            $validated = true;
        }

        if (! $account->public_key && $validated) {
            $account->public_key = ZnnUtilities::encodeData($publicKey);
            $account->save();
        }

        return $validated;
    }

    public static function broadcastSignedMessage(Account $account, string $title, string $post, string $message, string $signature): bool
    {
        $discourseApi = App::make('discourse.api');
        $pillar = Pillar::where('owner_id', $account->id)->first();
        $pillarMessage = $pillar->messages()->create([
            'title' => $title,
            'post' => $post,
            'message' => $message,
            'signature' => $signature,
            'is_public' => true,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $verificationLink = route('tools.verify-signature', [
            'address' => $account->address,
            'public_key' => $account->public_key,
            'message' => $message,
            'signature' => $signature,
        ]);
        $messageMarkdown = "{$post}
---
```
# Proof of Pillar
Pillar: {$pillar->name}
Address: {$account->address}
Public Key: {$account->public_key}
Message: {$message}
Signature: {$signature}
```
[Verify this message]({$verificationLink})";

        $forumPost = $discourseApi->createTopic($title, $messageMarkdown, 20, 'system', 0, now());

        if ($forumPost && $forumPost->http_code === 200) {
            $pillarMessage->is_broadcast = true;
            $pillarMessage->save();

            return true;
        }

        return false;
    }
}
