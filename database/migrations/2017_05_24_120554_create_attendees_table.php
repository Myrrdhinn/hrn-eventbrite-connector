<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->bigInteger('user_id');
            $table->bigInteger('order_id');
            $table->bigInteger('event_id');
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('fake_email')->nullable();
            $table->string('job_title')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('attendee_status')->nullable();
            $table->string('ticket_type')->nullable();
            $table->string('discount_code')->nullable();
            $table->string('ticket_base_price')->nullable();
            $table->string('ticket_eb_fee')->nullable();
            $table->string('ticket_payment_fee')->nullable();
            $table->string('ticket_tax')->nullable();
            $table->string('ticket_gross')->nullable();
            $table->string('refunded')->default('false');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendees');
    }
}
