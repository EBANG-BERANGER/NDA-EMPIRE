<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedInteger('price');
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at');
            $table->string('status')->default('pending'); // pending, confirmed, done, cancelled, no_show
            $table->string('note', 500)->nullable();
            $table->boolean('reminded')->default(false);
            $table->timestamps();
        });

        Schema::create('wigs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description', 1000)->nullable();
            $table->unsignedInteger('price');
            $table->string('image_path');
            $table->string('video_path')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wig_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('price');
            $table->string('status')->default('pending'); // pending, ready, collected, cancelled
            $table->string('note', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('tryons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wig_id')->constrained()->cascadeOnDelete();
            $table->string('result_path');
            $table->timestamps();
        });

        // Starter menu so the site isn't empty on day one; Niomba edits prices in /admin.
        $now = now();
        DB::table('services')->insert(array_map(fn ($s) => [
            'name' => $s[0], 'category' => $s[1], 'duration_minutes' => $s[2], 'price' => $s[3],
            'created_at' => $now, 'updated_at' => $now,
        ], [
            ['Pose de perruque (lace frontale)', 'Coiffure', 90, 25000],
            ['Installation closure', 'Coiffure', 90, 20000],
            ['Tresses knotless', 'Coiffure', 240, 35000],
            ['Brushing & coiffage perruque', 'Coiffure', 60, 10000],
            ['Pose de cils classique', 'Cils', 90, 20000],
            ['Pose de cils volume russe', 'Cils', 120, 30000],
            ['Remplissage cils', 'Cils', 60, 12000],
        ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('tryons');
        Schema::dropIfExists('portfolio_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('wigs');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('services');
    }
};
