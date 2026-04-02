<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'title', 'text_content', 'media_url'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class, 'post_tags');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }
}
