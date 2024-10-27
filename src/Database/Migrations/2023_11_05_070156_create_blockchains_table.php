<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockchainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blockchains', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->unsignedInteger('index'); // Index of the block
            $table->string('previous_hash'); // Previous block's hash
            $table->timestamp('timestamp'); // Timestamp of block creation
            $table->text('data'); // Data stored in the block (as JSON)
            $table->string('hash'); // Current block's hash
            $table->timestamps(); // Created at & Updated at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blockchains');
    }
}
