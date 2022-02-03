<?php

namespace Extensions\Subdomains;

use App\Models\Server;
use Illuminate\Http\Request;

interface Subdomain {
    public static function config();

    public static function seeder();

    /**
     * Return a list of subdomains
     */
    public static function subdomains();

    /**
     * Update the subdomain name of the server. Return true if
     * success, an error message if failed.
     */
    public static function update(Server $server, $name, $subdomain, $port);
    
    /**
     * Show the extension settings page in admin area
     */
    public static function show();

    /**
     * Update extension settings to the database
     */
    public static function store(Request $request);
}
