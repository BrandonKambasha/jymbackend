<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('groups'); // This will drop the groups table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('visibility');
            $table->unsignedBigInteger('owner_id');
            $table->integer('duration_days');
            $table->decimal('contribution', 10, 2);
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }
};