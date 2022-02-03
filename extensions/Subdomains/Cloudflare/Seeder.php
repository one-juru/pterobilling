<?php

namespace Extensions\Subdomains\Cloudflare;

use App\Models\Extension;
use Illuminate\Database\Seeder as DatabaseSeeder;

class Seeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;
        
        if (is_null(Extension::where(['extension' => 'Cloudflare', 'key' => 'email'])->first())) {
            Extension::create([
                'extension' => 'Cloudflare',
                'key' => 'email',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Extension::where(['extension' => 'Cloudflare', 'key' => 'api_key'])->first())) {
            Extension::create([
                'extension' => 'Cloudflare',
                'key' => 'api_key',
                'value' => null,
            ]);
            ++$i;
        }

        if ($i > 0)
            $this->command->info('Seeded and updated the extensions table for Cloudflare extension successfully!');
        else
            $this->command->line('Records of Cloudflare extension already exist in the extensions table. Skipped seeding!');
    }
}
