<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PteroBilling</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/themes/default/admin.css') }}" />
</head>

<body data-compagny="Pterobilling" r-load>
    <div id="app"></div>

    @foreach ($plugin_scripts as $script)
        <script type="application/javascript" src="{{ $script }}"></script>
    @endforeach

    <script type="application/javascript">
        {{ $loading_script }}
    </script>

    <script src="{{ mix('js/admin.js') }}"></script>
</body>

</html>
