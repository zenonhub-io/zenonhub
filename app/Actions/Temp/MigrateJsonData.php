<?php

declare(strict_types=1);

namespace App\Actions\Temp;

use App\Actions\Profile\UpdateUserNotificationSubscriptions;
use App\Models\Favorite;
use App\Models\Nom\Account;
use App\Models\NotificationType;
use App\Models\PlasmaBotEntry;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class MigrateJsonData
{
    use AsAction;

    public string $commandSignature = 'tools:migrate-json-data';

    private string $userJsonPath = 'users.json';

    private string $plasmaBotEntriesJsonPath = 'plasmabot-entries.json';

    public function handle(): void
    {
        $this->importUsers();
        $this->importPlasmaBotEntries();
    }

    private function importUsers(): void
    {
        $json = Storage::json($this->userJsonPath);

        collect($json)
            ->each(function ($item) {

                if ($item['username'] === 'digitalSloth') {
                    return;
                }

                $user = User::updateOrCreate([
                    'username' => $item['username'],
                ], [
                    'email' => $item['email'],
                    'password' => $item['password'],
                    'email_verified_at' => $item['email_verified_at'],
                    'privacy_confirmed_at' => $item['privacy_confirmed_at'],
                    'last_login_at' => $item['last_login_at'],
                    'last_seen_at' => $item['last_seen_at'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ]);

                if (! empty($item['notifications'])) {
                    $notificationTypes = NotificationType::whereIn('code', $item['notifications'])
                        ->get('id')
                        ->mapWithKeys(fn ($item) => [$item->id => true])
                        ->toArray();

                    (new UpdateUserNotificationSubscriptions)->update($user, $notificationTypes);
                }

                if (! empty($item['favorites'])) {
                    foreach ($item['favorites'] as $favoriteData) {
                        $account = Account::firstWhere('address', $favoriteData['address']);
                        $favorite = Favorite::add($account, $user);
                        $favorite->label = $favoriteData['label'];
                        $favorite->notes = $favoriteData['note'];
                        $favorite->save();
                    }
                }
            });
    }

    private function importPlasmaBotEntries(): void
    {
        $json = Storage::json($this->plasmaBotEntriesJsonPath);

        collect($json)
            ->sortBy('created_at')
            ->each(function ($item) {

                if (! $item['hash']) {
                    return;
                }

                $account = Account::firstWhere('address', $item['address']);

                if (! $account) {
                    return;
                }

                PlasmaBotEntry::UpdateOrInsert([
                    'account_id' => $account->id,
                    'hash' => $item['hash'],
                ], [
                    'is_confirmed' => 1,
                    'amount' => $item['amount'],
                    'expires_at' => $item['expires_at'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                    'deleted_at' => $item['deleted_at'],
                ]);
            });
    }
}
