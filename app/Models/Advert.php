<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'headline',
        'body',
        'cta_text',
        'cta_link',
        'image',
        'icon',
        'owner_name',
        'owner_contact',
        'placement',
        'display_order',
        'display_count',
        'starts_at',
        'ends_at',
        'is_confirmed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'id',
        'owner_name',
        'owner_contact',
        'display_order',
        'display_count',
        'account_block_id',
        'user_id',
    ];
}
