<?php

namespace App\Jobs;

use App\Models\Addon;
use App\Models\Plan;
use App\Models\Server;
use App\Models\ServerAddon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PterodactylSDK\PterodactylAPI;

class UpdateServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The server instance.
     *
     * @var \App\Models\Server
     */
    protected $server;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pterodactyl_api = new PterodactylAPI;
        $plan = Plan::find($this->server->plan_id);
        $cpu = $plan->cpu;
        $ram = $plan->ram;
        $swap = $plan->swap;
        $disk = $plan->disk;
        $io = $plan->io;
        $databases = $plan->databases;
        $backups = $plan->backups;
        $extra_ports = $plan->extra_ports;

        foreach (ServerAddon::where('server_id', $this->server->id) as $server_addon) {
            switch (($addon = Addon::find($server_addon->addon_id))->resource) {
                case 'ram':
                    $ram += $addon->amount * $server_addon->quantity;
                    break;
                case 'cpu':
                    $cpu += $addon->amount * $server_addon->quantity;
                    break;
                case 'disk':
                    $disk += $addon->amount * $server_addon->quantity;
                    break;
                case 'database':
                    $databases += $addon->amount * $server_addon->quantity;
                    break;
                case 'backup':
                    $backups += $addon->amount * $server_addon->quantity;
                    break;
                case 'extra_port':
                    $extra_ports += $addon->amount * $server_addon->quantity;
                    break;
                case 'dedicated_ip':
                    $allocation_id = $addon->amount;
                    break;
            }
        }

        if (isset($allocation_id)) {
            $build_api = $pterodactyl_api->servers()->editBuild($this->server->id, [
                'allocation' => $allocation_id,
                'cpu' => $cpu,
                'memory' => $ram,
                'swap' => $swap,
                'disk' => $disk,
                'io' => $io,
                'feature_limits' => [
                    'databases' => $databases,
                    'backups' => $backups,
                    'allocations' => $extra_ports + 1,
                ],
            ]);
        } else {
            $build_api = $pterodactyl_api->servers()->editBuild($this->server->id, [
                'cpu' => $cpu,
                'memory' => $ram,
                'swap' => $swap,
                'disk' => $disk,
                'io' => $io,
                'feature_limits' => [
                    'databases' => $databases,
                    'backups' => $backups,
                    'allocations' => $extra_ports + 1,
                ],
            ]);
        }

        if ($build_api->status() != '200' || !empty($build_api->errors())) {
            Log::error("An error occurred while getting egg details!");

            foreach ($build_api->errors() as $error) {
                Log::error($error);
            }

            return $this->fail();
        }

        $egg_api = $pterodactyl_api->nests()->eggsGet($this->server->nest_id, $this->server->egg_id);
        $egg = [];

        if ($egg_api->status() != '200' || !empty($egg_api->errors())) {
            Log::error("An error occurred while getting egg details!");

            foreach ($egg_api->errors() as $error) {
                Log::error($error);
            }

            return $this->fail();
        } else {
            $egg['startup'] = $egg_api->response()->attributes->startup;
            $egg['environment'] = [];
            $egg['docker_image'] = $egg_api->response()->attributes->docker_image;
            
            foreach ($egg_api->response()->attributes->relationships->variables->data as $key => $value) {
                $egg['environment'][$key] = $value;
            }
        }

        $startup_api = $pterodactyl_api->servers()->editStartup($this->server->id, [
            'startup' => $egg['startup'],
            'environment' => $egg['environment'],
            'egg' => $this->server->egg_id,
            'image' => $egg['docker_image'],
            'skip_scripts' => true,
        ]);

        if ($startup_api->status() != '200' || !empty($startup_api->errors())) {
            Log::error("An error occurred while getting egg details!");

            foreach ($startup_api->errors() as $error) {
                Log::error($error);
            }

            return $this->fail();
        }
        
        $this->server->status = 0;
        $this->server->save();
    }
}
