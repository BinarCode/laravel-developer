<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelDeveloperTable extends Migration
{
    public function up()
    {
        Schema::create('exception_logs', function (Blueprint $table) {
           $table->id();
           $table->uuid('uuid');

           $table->string('name')->nullable();
           $table->string('tags')->nullable();
           $table->text('payload')->nullable();
           $table->text('exception')->nullable();
           $table->text('previous')->nullable();
           $table->string('file')->nullable();
           $table->string('line')->nullable();
           $table->string('code')->nullable();

           $table->bigInteger('created_by')->nullable();
           $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exception_logs');
    }
}
