<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("user_id");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            
            $table->string("first_name");
            $table->string("last_name")->nullable();
            $table->string("phone");
            $table->string("email")->nullable();
            $table->text("address")->nullable();
            $table->date("dob")->nullable();
            $table->text("notes")->nullable();
            $table->boolean("started")->default(false);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('contacts');
    }
};
