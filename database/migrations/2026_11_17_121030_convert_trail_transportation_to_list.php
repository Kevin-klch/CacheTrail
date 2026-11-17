<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A trail can now have several means of transport, stored as a JSON list.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->json('transportations')->nullable()->after('transportation');
        });

        $mapping = [
            'foot' => ['foot'],
            'bike' => ['bike'],
            'foot_and_bike' => ['foot', 'bike'],
        ];

        foreach ($mapping as $old => $new) {
            DB::table('trails')->where('transportation', $old)->update(['transportations' => json_encode($new)]);
        }

        Schema::table('trails', function (Blueprint $table) {
            $table->dropColumn('transportation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->string('transportation')->default('foot')->after('transportations');
        });

        DB::table('trails')->orderBy('id')->each(function (object $trail): void {
            $modes = json_decode($trail->transportations ?? '[]', true);
            $old = match (true) {
                in_array('foot', $modes, true) && in_array('bike', $modes, true) => 'foot_and_bike',
                in_array('bike', $modes, true) => 'bike',
                default => 'foot',
            };

            DB::table('trails')->where('id', $trail->id)->update(['transportation' => $old]);
        });

        Schema::table('trails', function (Blueprint $table) {
            $table->dropColumn('transportations');
        });
    }
};
