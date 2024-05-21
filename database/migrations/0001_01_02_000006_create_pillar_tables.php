<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nom_pillars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->references('id')->on('nom_accounts');
            $table->foreignId('producer_account_id')->nullable()->references('id')->on('nom_accounts');
            $table->foreignId('withdraw_account_id')->nullable()->references('id')->on('nom_accounts');
            $table->string('name')->index();
            $table->string('slug')->index();
            $table->bigInteger('qsr_burn')->default(15000000000000);
            $table->bigInteger('weight')->index()->default(0);
            $table->integer('produced_momentums')->default(0);
            $table->integer('expected_momentums')->default(0);
            $table->integer('missed_momentums')->default(0);
            $table->integer('momentum_rewards')->default(0);
            $table->integer('delegate_rewards')->default(0);
            $table->decimal('az_engagement', 5)->nullable()->index();
            $table->bigInteger('az_avg_vote_time')->nullable()->index();
            $table->integer('avg_momentums_produced')->default(0)->index();
            $table->bigInteger('total_momentums_produced')->default(0)->index();
            $table->boolean('is_legacy')->default(0);
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('created_at')->default('2021-11-24 12:00:00');
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('nom_pillar_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_id')->nullable()->references('id')->on('nom_pillars')->cascadeOnDelete();
            $table->foreignId('producer_account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('withdraw_account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->integer('momentum_rewards')->default(0);
            $table->integer('delegate_rewards')->default(0);
            $table->boolean('is_reward_change')->default(0);
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('nom_delegations', function (Blueprint $table) {
            $table->foreignId('pillar_id')->nullable()->references('id')->on('nom_pillars')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->index(['pillar_id', 'account_id']);
        });

        Schema::create('nom_pillar_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_id')->nullable()->references('id')->on('nom_pillars')->cascadeOnDelete();
            $table->string('title')->index();
            $table->text('post');
            $table->string('message');
            $table->string('signature');
            $table->timestamp('created_at')->nullable();
        });

        Schema::table('nom_votes', function (Blueprint $table) {
            $table->foreignId('pillar_id')->after('owner_id')->nullable()->references('id')->on('nom_pillars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nom_votes', function (Blueprint $table) {
            $table->dropForeign(['pillar_id']);
        });

        Schema::dropIfExists('nom_pillar_messages');
        Schema::dropIfExists('nom_pillar_histories');
        Schema::dropIfExists('nom_delegations');
        Schema::dropIfExists('nom_pillars');
    }
};
