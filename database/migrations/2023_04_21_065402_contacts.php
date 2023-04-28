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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->integer("contact_id")->nullable();
            $table->string("name")->nullable();
            $table->string("email");
            $table->string("phone")->nullable();
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            $table->integer("responsible_user_id")->nullable();
            $table->integer("group_id")->nullable();
            $table->integer("created_by")->nullable();
            $table->integer("updated_by")->nullable();
            $table->string("closest_task_at")->nullable();
            $table->string("custom_fields_values")->nullable();
            $table->integer("account_id")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
