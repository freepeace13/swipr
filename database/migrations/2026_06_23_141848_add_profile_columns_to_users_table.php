<?php

use App\Enums\InterestedIn;
use App\Enums\LookingFor;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->date('birthdate');
            $table->string('gender');
            $table->string('looking_for')->default(LookingFor::default()->value);
            $table->string('interested_in')->default(InterestedIn::default()->value);
            $table->tinyInteger('min_age_preference')->default(18);
            $table->tinyInteger('max_age_preference')->default(99);
            $table->boolean('flexible_on_age')->default(false);
            $table->timestamp('last_seen_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('bio');
            $table->dropColumn('birthdate');
            $table->dropColumn('gender');
            $table->dropColumn('looking_for');
            $table->dropColumn('interested_in');
            $table->dropColumn('min_age_preference');
            $table->dropColumn('max_age_preference');
            $table->dropColumn('flexible_on_age');
            $table->dropColumn('last_seen_at');
        });
    }
};
