<?php

namespace App\Models;

use Database\Factories\AmigoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Amigo extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return AmigoFactory::new();
    }

    protected $table = 'amigos';

    protected $fillable = [
        'user_id',
        'friend_id'
    ];

    /**
     * Normalize pair ordering so that user_id <= friend_id
     * and provide relations to User.
     */
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($model) {
            if ($model->user_id > $model->friend_id) {
                [$model->user_id, $model->friend_id] = [$model->friend_id, $model->user_id];
            }
        });

        static::updating(function ($model) {
            if ($model->user_id > $model->friend_id) {
                [$model->user_id, $model->friend_id] = [$model->friend_id, $model->user_id];
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public function isFriend(User $user): bool
    {
        $currentId = $this->id;
        $userId = $user->id;
        if ($currentId > $userId) [$currentId, $userId] = [$userId, $currentId];
        return self::where('user_id', $currentId)->where('friend_id', $userId)->exists();
    }

    /**
     * Check whether unordered pair exists (returns bool).
     */
    public static function pairExists(int $a, int $b): bool
    {
        if ($a > $b) [$a, $b] = [$b, $a];
        return self::where('user_id', $a)->where('friend_id', $b)->exists();
    }
}
