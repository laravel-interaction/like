<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            config('like.table_names.pivot'),
            static function (Blueprint $table): void {
                config('like.uuids') ? $table->uuid('uuid') : $table->bigIncrements('id');
                $table->unsignedBigInteger(config('like.column_names.user_foreign_key'))
                    ->index();
                $table->morphs('likeable');
                $table->timestamps();
                $table->unique([config('like.column_names.user_foreign_key'), 'likeable_type', 'likeable_id']);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('like.table_names.likes'));
    }
}
