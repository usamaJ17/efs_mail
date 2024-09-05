<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();

            $table->string('subject');

            $table->foreignId('from')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // read or unread
            $table->boolean('is_read')->default(false);
            // sent or draft
            $table->boolean('is_sent')->default(false);
            // important or not
            $table->boolean('is_important')->default(false);
            // starred
            $table->boolean('is_starred')->default(false);

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('group_id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
