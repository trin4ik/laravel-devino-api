<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevinoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_devino', function (Blueprint $table) {
            $table->id();
            $table->char('from', 11)->default('');
            $table->char('to', 11)->index()->default(0);
            $table->text('message')->default('');
            $table->enum('status', ['new', 'scheduled', 'enroute', 'sent', 'delivered', 'expired', 'undeliverable', 'rejected', 'deleted', 'unknown'])->default('new');
            $table->json('extra');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_devino');
    }
}
