<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Check;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Compte Démo', 'password' => Hash::make('password')]
        );

        $probes = [
            ['name' => 'Site vitrine', 'type' => 'http', 'target' => 'https://example.com', 'base' => 180, 'outage' => false],
            ['name' => 'API publique', 'type' => 'http', 'target' => 'https://api.github.com', 'base' => 320, 'outage' => true],
            ['name' => 'Resolveur DNS (TCP)', 'type' => 'tcp', 'target' => '1.1.1.1:53', 'base' => 25, 'outage' => false],
        ];

        foreach ($probes as $data) {
            $probe = $user->probes()->firstOrCreate(
                ['name' => $data['name']],
                ['type' => $data['type'], 'target' => $data['target'], 'check_interval_minutes' => 5]
            );

            if ($probe->checks()->exists()) {
                continue;
            }

            $rows = [];

            // 48 heures d'historique, une vérification toutes les 30 minutes
            for ($i = 96; $i >= 0; $i--) {
                $down = $data['outage'] && in_array($i, [40, 41], true);

                $rows[] = [
                    'probe_id' => $probe->id,
                    'is_up' => ! $down,
                    'response_time_ms' => $down ? null : (int) ($data['base'] * (0.8 + mt_rand(0, 60) / 100)),
                    'status_code' => $data['type'] === 'http' ? ($down ? 503 : 200) : null,
                    'error_message' => $down ? 'Code HTTP 503' : null,
                    'checked_at' => now()->subMinutes($i * 30),
                ];
            }

            Check::insert($rows);
        }
    }
}
