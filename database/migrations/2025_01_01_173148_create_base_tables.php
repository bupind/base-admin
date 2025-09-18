<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTables extends Migration
{
    public function getConnection()
    {
        return config('backend.database.connection') ?: config('database.default');
    }

    public function up()
    {
        Schema::create(config('backend.database.users_table'), function(Blueprint $table) {
            $table->increments('id');
            $table->string('username', 190)->unique();
            $table->string('password', 60);
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
        Schema::create(config('backend.database.roles_table'), function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });
        Schema::create(config('backend.database.permissions_table'), function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->string('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->timestamps();
        });
        Schema::create(config('backend.database.menu_table'), function(Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('icon', 50);
            $table->string('uri')->nullable();
            $table->string('permission')->nullable();
            $table->timestamps();
        });
        Schema::create(config('backend.database.role_users_table'), function(Blueprint $table) {
            $table->integer('role_id');
            $table->integer('user_id');
            $table->index(['role_id', 'user_id']);
            $table->timestamps();
        });
        Schema::create(config('backend.database.role_permissions_table'), function(Blueprint $table) {
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->index(['role_id', 'permission_id']);
            $table->timestamps();
        });
        Schema::create(config('backend.database.user_permissions_table'), function(Blueprint $table) {
            $table->integer('user_id');
            $table->integer('permission_id');
            $table->index(['user_id', 'permission_id']);
            $table->timestamps();
        });
        Schema::create(config('backend.database.role_menu_table'), function(Blueprint $table) {
            $table->integer('role_id');
            $table->integer('menu_id');
            $table->index(['role_id', 'menu_id']);
            $table->timestamps();
        });
        Schema::create(config('backend.database.operation_log_table'), function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('path');
            $table->string('method', 10);
            $table->string('ip');
            $table->text('input');
            $table->index('user_id');
            $table->timestamps();
        });
        Schema::create('sessions', function(Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('backend.database.users_table'));
        Schema::dropIfExists(config('backend.database.roles_table'));
        Schema::dropIfExists(config('backend.database.permissions_table'));
        Schema::dropIfExists(config('backend.database.menu_table'));
        Schema::dropIfExists(config('backend.database.user_permissions_table'));
        Schema::dropIfExists(config('backend.database.role_users_table'));
        Schema::dropIfExists(config('backend.database.role_permissions_table'));
        Schema::dropIfExists(config('backend.database.role_menu_table'));
        Schema::dropIfExists(config('backend.database.operation_log_table'));
        Schema::dropIfExists('sessions');
    }
}
