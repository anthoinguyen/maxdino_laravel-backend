<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerDeleteUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER before_users_delete BEFORE DELETE ON `users` FOR EACH ROW
        BEGIN
            DECLARE count_admin INT(11);
            SET count_admin = (SELECT COUNT(*) FROM users WHERE admin = 1);
                IF (count_admin <= 1 && OLD.admin = 1) THEN
                    BEGIN
    		            SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Can not delete user';
                    END;
                ELSE
                    BEGIN
                   		DELETE FROM asks WHERE asks.user_id = old.id;
                        DELETE FROM learns WHERE learns.user_id = old.id;
                        DELETE FROM videos WHERE videos.user_id = old.id;
                        DELETE FROM comments WHERE comments.user_id = old.id;
                        DELETE FROM reactions WHERE reactions.user_id = old.id;
                        DELETE FROM password_resets WHERE password_resets.email = old.email;
                    END;
            END IF;
        END
        ");
    }

    // DELETE FROM `comments` WHERE `comments.user_id = users.id`;
    // DELETE FROM `reactions` WHERE `reactions.user_id = users.id`;
    //
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `before_users_delete`');
    }
}
