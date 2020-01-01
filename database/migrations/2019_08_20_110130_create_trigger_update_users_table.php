<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerUpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER after_users_update AFTER UPDATE ON `users` FOR EACH ROW
        BEGIN
            DECLARE count_admin INT(11);
            SET count_admin = (SELECT COUNT(*) FROM users WHERE admin = 1);
                IF (count_admin < 1 && OLD.admin = 1) THEN
                    BEGIN
    		            SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Can not edit user';
                    END;
                ELSE
                    BEGIN
                    END;
            END IF;
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
        Schema::dropIfExists('after_users_update');
    }
}
