<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('secret', 100)->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect');
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');
            $table->timestamps();
        });

        DB::table('oauth_clients')->insert([
            [
                'id' => 1,
                'user_id' => null,
                'name' => 'Laravel Personal Access Client',
                'secret' => 'yHFLw6ccVO9F3I3bfrbQg1OrdpptrdosftAYMPAL',
                'provider' => null,
                'redirect' => 'http://localhost',
                'personal_access_client' => true,
                'password_client' => false,
                'revoked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => null,
                'name' => 'Laravel Password Grant Client',
                'secret' => 'ykPS84Z1zsVv3gPqGfEvw8ymQld7UW8HcGfwvR2q',
                'provider' => 'users',
                'redirect' => 'http://localhost',
                'personal_access_client' => false,
                'password_client' => true,
                'revoked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_clients');
    }
};
