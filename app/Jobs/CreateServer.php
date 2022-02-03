<?php

namespace App\Jobs;

use App\Models\Addon;
use App\Models\Client;
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

class CreateServer implements ShouldQueue
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
        $dedi_ip = null;

        foreach (ServerAddon::where('server_id', $this->server->id) as $server_addon) {
            switch (($addon = Addon::find($server_addon->addon_id))->resource) {
                case 'ram':
                    $ram += $addon->amount * $server_addon->value;
                    break;
                case 'cpu':
                    $cpu += $addon->amount * $server_addon->value;
                    break;
                case 'disk':
                    $disk += $addon->amount * $server_addon->value;
                    break;
                case 'database':
                    $databases += $addon->amount * $server_addon->value;
                    break;
                case 'backup':
                    $backups += $addon->amount * $server_addon->value;
                    break;
                case 'extra_port':
                    $extra_ports += $addon->amount * $server_addon->value;
                    break;
                case 'dedicated_ip':
                    $dedi_ip = $server_addon->value;
                    break;
            }
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
            $egg['docker_image'] = ($egg_res = $egg_api->response())->attributes->docker_image;
            $egg['startup'] = $egg_res->attributes->startup;
            $egg['environment'] = [];

            foreach ($egg_res->attributes->relationships->variables->data as $var) {
                $egg['environment'][$var->attributes->env_variable] = $var->attributes->default_value;
            }
        }

        $ips = Addon::dediIpList();
        $allocation_id = (function () use ($pterodactyl_api, $plan, $dedi_ip, $ips) {
			$page = $pages = 1;
			
			while ($page <= $pages) {
				$allocation_api = $pterodactyl_api->nodes($page)->allocationsGetAll($this->server->node_id);
                if ($allocation_api->status() != '200' || !empty($allocation_api->errors())) {
                    Log::error("An error occurred while getting a node and its allocations!");
        
                    foreach ($allocation_api->errors() as $error) {
                        Log::error($error);
                    }
                } else {
                    $pages = ($allocation_res = $allocation_api->response())->meta->pagination->total_pages;
                    foreach ($allocation_res->data as $allocation) {
                        if ($allocation->attributes->assigned) continue;
                        if ($dedi_ip && $allocation->attributes->ip != $dedi_ip) continue;
                        if ($plan->min_port && $allocation->attributes->port < $plan->min_port) continue;
                        if ($plan->max_port && $allocation->attributes->port > $plan->max_port) continue;
                        if (is_null($dedi_ip) && in_array($allocation->attributes->ip, $ips)) continue;
                        
                        return $allocation->attributes->id;
                    }
                    $page++;
                }
			}
			
			return null;
		})();

        if (is_null($allocation_id)) return $this->fail();

        $server_api = $pterodactyl_api->servers()->add([
            'name' => $this->server->server_name,
            'user' => Client::find($this->server->client_id)->user_id,
            'egg' => $this->server->egg_id,
            'docker_image' => $egg['docker_image'],
            'startup' => $egg['startup'],
            'environment' => $egg['environment'],
            'limits' => [
                'cpu' => $cpu,
                'memory' => $ram,
                'swap' => $swap,
                'disk' => $disk,
                'io' => $io,
            ],
            'feature_limits' => [
                'databases' => $databases,
                'backups' => $backups,
                'allocations' => $extra_ports + 1,
            ],
            'allocation' => [
                'default' => $allocation_id,
            ],
        ]);

        if ($server_api->status() != '201' || !empty($server_api->errors())) {
            Log::error("An error occurred while creating a server!");

            foreach ($server_api->errors() as $error) {
                Log::error($error);
            }
            
            return $this->fail();
        }

        $this->server->server_id = ($server_attr = $server_api->response()->attributes)->id;
        $this->server->identifier = $server_attr->identifier;
        $this->server->status = 0;
        $this->server->save();
    }
}
