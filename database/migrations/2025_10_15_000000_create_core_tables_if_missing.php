<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->string('role')->nullable();
                $table->string('google_id')->nullable();
                $table->string('avatar')->nullable();
                $table->string('uid')->unique();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_accesses')) {
            Schema::create('user_accesses', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->boolean('classmate')->default(false);
                $table->boolean('leaderboard')->default(false);
                $table->boolean('post')->default(false);
                $table->boolean('practice')->default(false);
                $table->boolean('exams')->default(false);
                $table->boolean('results')->default(false);
                $table->boolean('mcq_management')->default(false);
                $table->boolean('read_access')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mcq_sets')) {
            Schema::create('mcq_sets', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title')->nullable();
                $table->string('category')->nullable();
                $table->string('exam_name')->nullable();
                $table->date('exam_date')->nullable();
                $table->string('exam_time')->nullable();
                $table->unsignedInteger('total_marks')->nullable();
                $table->unsignedInteger('duration')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mcq_questions')) {
            Schema::create('mcq_questions', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('mcq_set_id');
                $table->text('question');
                $table->text('ans_1');
                $table->text('ans_2');
                $table->text('ans_3');
                $table->text('ans_4');
                $table->string('correct_ans');
                $table->unsignedInteger('marks')->default(1);
                $table->text('notes')->nullable();
                $table->text('report')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }

        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table): void {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            });
        }

        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table): void {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            });
        }

        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table): void {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_role_primary');
            });
        }
    }

    public function down(): void
    {
        // Intentionally left blank; legacy tables should not be dropped automatically.
    }
};
