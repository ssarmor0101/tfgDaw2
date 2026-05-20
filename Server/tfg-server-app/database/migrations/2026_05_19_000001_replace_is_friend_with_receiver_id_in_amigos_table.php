<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add receiver_id (nullable — NULL means confirmed, not-null means pending)
        Schema::table('amigos', function (Blueprint $table) {
            $table->unsignedBigInteger('receiver_id')->nullable()->after('friend_id');
        });

        // 2. Migrate existing data:
        //    is_friend = false (pending) → receiver_id = friend_id  (best-effort: higher-ID user is the receiver)
        //    is_friend = true  (confirmed) → receiver_id stays NULL  (already null by default)
        DB::table('amigos')->where('is_friend', false)->update([
            'receiver_id' => DB::raw('friend_id'),
        ]);

        // 3. Drop the old boolean column
        Schema::table('amigos', function (Blueprint $table) {
            $table->dropColumn('is_friend');
        });
    }

    public function down(): void
    {
        // 1. Re-add is_friend
        Schema::table('amigos', function (Blueprint $table) {
            $table->boolean('is_friend')->default(false);
        });

        // 2. Restore values: receiver_id IS NULL → is_friend = true
        DB::table('amigos')->whereNull('receiver_id')->update(['is_friend' => true]);

        // 3. Drop receiver_id
        Schema::table('amigos', function (Blueprint $table) {
            $table->dropColumn('receiver_id');
        });
    }
};
