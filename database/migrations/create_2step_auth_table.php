<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(config('2step.table_name'), function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable', '2step_verify_type_auth_id_index');
            $table->string('channel')->default('email');    
            $table->string('code')->nullable();
            $table->integer('count')->default(0);
            $table->dateTime('enabled_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('request_at')->nullable();
            $table->timestamps();
        });
    }
};
