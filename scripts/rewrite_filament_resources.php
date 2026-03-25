<?php

$files = glob(__DIR__ . '/../Modules/*/app/Filament/Resources/*/*Resource.php');

foreach ($files as $file) {
    $code = file_get_contents($file);

    $code = str_replace(
        [
            "use Filament\\Resources\\Resource;\n",
            "use Filament\\Support\\Icons\\Heroicon;\n",
            "use Illuminate\\Database\\Eloquent\\Builder;\n",
            "use Illuminate\\Database\\Eloquent\\SoftDeletingScope;\n",
            "use BackedEnum;\n",
        ],
        [
            "use Modules\\Core\\Filament\\Support\\ModuleResource;\n",
            '',
            '',
            '',
            '',
        ],
        $code,
    );

    $code = preg_replace(
        '/class (\\w+) extends Resource/',
        'class $1 extends ModuleResource',
        $code,
    );

    $code = preg_replace(
        '/\n\s*protected static string\|BackedEnum\|null \$navigationIcon = Heroicon::OutlinedRectangleStack;\n/',
        "\n",
        $code,
    );

    $code = preg_replace(
        '/\n\s*public static function getRecordRouteBindingEloquentQuery\(\): Builder\n\s*\{\n.*?\n\s*\}\n/s',
        "\n",
        $code,
    );

    $code = preg_replace(
        "/\n{3,}/",
        "\n\n",
        $code,
    );

    file_put_contents($file, $code);
}
