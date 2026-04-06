<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * The "booted" method of the model.
     * Edge Case: When an interest is deleted, unsubscribe all users from Firebase Topics.
     */
    protected static function booted(): void
    {
        static::deleting(function (Interest $interest) {
            // Fetch all tokens for all users subscribed to this interest
            $tokens = DeviceToken::whereIn('user_id', $interest->users()->pluck('users.id'))->pluck('fcm_token')->toArray();

            if (!empty($tokens)) {
                $fcm = app(\App\Services\FcmDeliveryService::class);
                $fcm->unsubscribeFromTopic("interest_{$interest->id}", $tokens);
            }
        });
    }

    public function users(): BelongsToMany

    {
        return $this->belongsToMany(User::class, 'user_interests');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }
}
