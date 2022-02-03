<?php

namespace Extensions\Softwares\Minecraft;

use Extensions\Softwares\Software;
use Illuminate\Support\Str;

class Controller implements Software
{
    public static $display_name = 'Minecraft';

    public static function softwares()
    {
        return [
            'PaperMC' => ['1.17.1', '1.16.5'],
        ];
    }

    public static function install($software, $version)
    {
        return Str::lower($software) . '_' . Str::lower($version) . '.jar';
    }
}
