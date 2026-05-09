<?php

namespace App\Models;

use App\DTOs\User\UserDto;
use App\Helpers\AmigoHelper;
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
        'friend_id',
        'is_friend'
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

    public function isFriend(UserDto $user): bool
    {
        $currentUser = $this->user;
        $currentUserDto = UserDto::fromModel($currentUser);
        [$currentUserDto, $user] = AmigoHelper::orderUsersPairForAmigo($currentUserDto, $user);
        $friend = self::where('user_id', $currentUserDto->id)->where('friend_id', $user->id)->first();
        return $friend != null && $friend->is_friend;
    }

    /**
     * Check whether unordered pair exists (returns bool).
     */
    public static function pairExists(UserDto $user, UserDto $friend): bool
    {
        [$a, $b] = AmigoHelper::orderUsersPairForAmigo($user, $friend);
        return self::where('user_id', $a->id)->where('friend_id', $b->id)->exists();
    }
}
