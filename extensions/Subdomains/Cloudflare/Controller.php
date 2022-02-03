<?php

namespace Extensions\Subdomains\Cloudflare;

use App\Http\Controllers\Api\ApiController;
use App\Models\Extension;
use App\Models\Server;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\Zones;
use Extensions\Subdomains\Subdomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PterodactylSDK\PterodactylAPI;

class Controller extends ApiController implements Subdomain
{
    public static $display_name = 'Cloudflare';

    public static function config() {
        return require __DIR__ . '/config.php';
    }

    public static function seeder() {
        return Seeder::class;
    }

    public static function subdomains()
    {
        $key = new APIToken(config('extensions.Cloudflare.email'), config('extensions.Cloudflare.api_key'));
        $adapter = new Guzzle($key);
        $zones = new Zones($adapter);
        $domains = $zones->listZones('', 'active', 1, 50);
        $subdomains = [];

        if ($domains->success && empty($domains->errors))
            foreach ($domains->result as $zone)
                array_push($subdomains, $zone->name);

        return $subdomains;
    }

    /**
     * Update the subdomain name of the server. Return true if
     * success, false or an error message if failed.
     */
    public static function update(Server $server, $name, $subdomain, $port)
    {
        $key = new APIToken(config('extensions.Cloudflare.email'), config('extensions.Cloudflare.api_key'));
        $adapter = new Guzzle($key);
        $zones = new Zones($adapter);
        $zone_id = $zones->getZoneID($subdomain);
        $dns = new DNS($adapter);
        $record_id = $dns->getRecordID($zone_id, 'SRV', "_minecraft._tcp.${name}");

        $pterodactyl_api = new PterodactylAPI;
        $node_api = $pterodactyl_api->servers()->get($server->server_id);
        if ($node_api->status() !== '200' && !empty($node_api->errors())) return false;
        $fqdn_api = $pterodactyl_api->nodes()->get($node_api->response()->attributes->node);
        if ($fqdn_api->status() !== '200' && !empty($fqdn_api->errors())) return false;

        $fqdn = $fqdn_api->response()->attributes->fqdn;

        if ($server->subdomain_name != $name) {
            if ($record_id) return 'The subdomain name has been taken!';

            if (!$dns->addRecord($zone_id, 'SRV', "_minecraft._tcp.${name}", "0 5 ${port} subdomain.${fqdn}", 1, false, 0)) return false;

            return $dns->deleteRecord($zone_id, $dns->getRecordID($zone_id, 'SRV', '_minecraft._tcp.'.$server->subdomain_name));
        } else {
            $result = $dns->updateRecordDetails($zone_id, $record_id, [
                'type' => 'SRV',
                'name' => "_minecraft._tcp.${name}",
                'content' => "0 5 ${port} subdomain.${fqdn}",
                'ttl' => 1,
                'proxied' => false,
            ]);

            return $result->success && empty($result->errors);
        }
    }

    public static function show()
    {
        return view('extensions.Cloudflare.show');
    }

    public static function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'api_key' => 'required|string',
        ]);

        if ($validator->fails())
            return ['errors' => $validator->errors()->all()];
            
        self::saveSetting('email', $request->input('email'));
        self::saveSetting('api_key', $request->input('api_key'));

        return ['success' => 'You have updated the Cloudflare extension settings successfully! Please click \'Reload Config\' above on the navigation bar to apply the changes.'];
    }

    /**
     * Additional functions
     */
    private static function saveSetting($key, $value)
    {
        $setting = Extension::where(['extension' => 'Cloudflare', 'key' => $key])->first();
        $setting->value = $value;
        $setting->save();
    }
}
