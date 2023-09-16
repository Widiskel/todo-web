<?php

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
        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->date('date');
            $table->foreignId('user_id')
            ->constrained(table: 'users', indexName: 'daily_task_user_id')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('task_id')
            ->constrained(table: 'tasks', indexName: 'daily_task_task_id')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_tasks');
    }
};
