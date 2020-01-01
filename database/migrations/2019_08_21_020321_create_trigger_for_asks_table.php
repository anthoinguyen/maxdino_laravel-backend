<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerForAsksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER before_asks_delete BEFORE DELETE ON asks FOR EACH ROW
        BEGIN
            DELETE FROM reactions WHERE reactions.ask_id = old.id;
            DELETE FROM comments WHERE comments.ask_id = old.id;
        END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `before_asks_delete`');
    }
}
