<?php

namespace Extensions;

class ExtensionManager
{
    /**
     * The extensions that manage custom payment gateways.
     */
    public static $gateways = [
        \Extensions\Gateways\PayPal\Controller::class,
    ];

    /**
     * The extensions that manage server subdomain names.
     */
    public static $subdomains = [
        \Extensions\Subdomains\Cloudflare\Controller::class,
    ];

    /**
     * The extensions that upload custom softwares to servers.
     */
    public static $softwares = [
        \Extensions\Softwares\Minecraft\Controller::class,
    ];

    /**
     * The extensions that manage client registrations and/or logins.
     */
    public static $auth = [
    ];

    /**
     * The extensions that manage custom email notifications.
     */
    public static $email = [
    ];

    /**
     * The extensions that are not classified in the above categories.
     */
    public static $general = [
    ];


    public static function getAllExtensions()
    {
        return array_merge(self::$gateways, self::$subdomains, self::$softwares, self::$auth, self::$email, self::$general);
    }
    
    public static function getAllExtensionsWithSettings()
    {
        return array_merge(self::$gateways, self::$subdomains, self::$auth, self::$email);
    }

    public static function getExtension($id)
    {
        foreach (self::getAllExtensions() as $extension) if ($extension::$display_name == $id) return $extension;
    }

    public static function getGatewayExtension($id)
    {
        foreach (self::$gateways as $extension) if ($extension::$display_name == $id) return $extension;
    }

    public static function getSubdomainExtension($id)
    {
        foreach (self::$subdomains as $extension) if ($extension::$display_name == $id) return $extension;
    }

    public static function getSoftwareExtension($id)
    {
        foreach (self::$softwares as $extension) if ($extension::$display_name == $id) return $extension;
    }

    public static function getAuthExtension($id)
    {
        foreach (self::$auth as $extension) if ($extension::$display_name == $id) return $extension;
    }

    public static function getEmailExtension($id)
    {
        foreach (self::$email as $extension) if ($extension::$display_name == $id) return $extension;
    }

    public static function getGeneralExtension($id)
    {
        foreach (self::$general as $extension) if ($extension::$display_name == $id) return $extension;
    }

    public static function getAllSeeders()
    {
        $seeders = [];
        foreach (self::getAllExtensions() as $extension) if (method_exists($extension, 'seeder')) array_push($seeders, $extension::seeder());
        return $seeders;
    }

    public static function fetchAllRoutes()
    {
        foreach (self::getAllExtensions() as $extension) if (method_exists($extension, 'routes')) $extension::routes();
    }
}
