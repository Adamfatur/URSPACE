<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adding foreign keys after table creation is not supported on SQLite.
        // This migration is meant for MySQL / MariaDB.
        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (!Schema::hasTable('spaces')) {
            return;
        }

        // threads.space_id -> spaces.id
        if (Schema::hasTable('threads') && Schema::hasColumn('threads', 'space_id')) {
            if (!$this->foreignKeyExists('threads', 'threads_space_id_foreign')) {
                Schema::table('threads', function (Blueprint $table) {
                    $table->foreign('space_id')
                        ->references('id')
                        ->on('spaces')
                        ->onDelete('cascade');
                });
            }
        }

        // space_members.space_id -> spaces.id
        if (Schema::hasTable('space_members') && Schema::hasColumn('space_members', 'space_id')) {
            if (!$this->foreignKeyExists('space_members', 'space_members_space_id_foreign')) {
                Schema::table('space_members', function (Blueprint $table) {
                    $table->foreign('space_id')
                        ->references('id')
                        ->on('spaces')
                        ->onDelete('cascade');
                });
            }
        }

        // space_members.user_id -> users.id
        if (Schema::hasTable('space_members') && Schema::hasColumn('space_members', 'user_id')) {
            if (!$this->foreignKeyExists('space_members', 'space_members_user_id_foreign')) {
                Schema::table('space_members', function (Blueprint $table) {
                    $table->foreign('user_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (Schema::hasTable('threads')) {
            Schema::table('threads', function (Blueprint $table) {
                // dropForeign accepts the index name
                $table->dropForeign('threads_space_id_foreign');
            });
        }

        if (Schema::hasTable('space_members')) {
            Schema::table('space_members', function (Blueprint $table) {
                $table->dropForeign('space_members_space_id_foreign');
                $table->dropForeign('space_members_user_id_foreign');
            });
        }
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $dbName = DB::getDatabaseName();

        $row = DB::selectOne(
            'SELECT CONSTRAINT_NAME as name FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = "FOREIGN KEY" LIMIT 1',
            [$dbName, $table, $constraintName]
        );

        return $row !== null;
    }
};
