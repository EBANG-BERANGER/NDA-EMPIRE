<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Niomba also sells bundles ("Hair Vendor"): only wigs can be tried on.
        Schema::table('wigs', function (Blueprint $table) {
            $table->string('kind', 10)->default('wig')->after('name'); // wig, bundle
        });

        // Swap the placeholder menu for the services she actually shows on TikTok,
        // but only if nobody has booked yet (never rewrite a menu clients used).
        if (DB::table('bookings')->exists()) {
            return;
        }
        DB::table('services')->delete();
        $now = now();
        DB::table('services')->insert(array_map(fn ($s) => [
            'name' => $s[0], 'category' => $s[1], 'duration_minutes' => $s[2], 'price' => $s[3],
            'created_at' => $now, 'updated_at' => $now,
        ], [
            ['Pose de perruque frontale', 'Coiffure', 90, 25000],
            ['Pose de perruque closure', 'Coiffure', 75, 20000],
            ['Sew-in avec leave out', 'Coiffure', 180, 35000],
            ['Ponytail frontale', 'Coiffure', 90, 20000],
            ['Personnalisation de perruque (nœuds, plucking, coupe)', 'Coiffure', 120, 20000],
            ['Coiffage de perruque (boucles, lissage)', 'Coiffure', 60, 10000],
            ['Tresses knotless', 'Coiffure', 240, 35000],
            ['Pose de cils classique', 'Cils', 90, 20000],
            ['Pose de cils volume russe', 'Cils', 120, 30000],
            ['Remplissage cils', 'Cils', 60, 12000],
        ]));
    }

    public function down(): void
    {
        Schema::table('wigs', fn (Blueprint $table) => $table->dropColumn('kind'));
    }
};
