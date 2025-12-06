<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VueEnums extends Command
{
    protected $signature = 'vue:enums {--path=app/Enums} {--output=resources/js/Enum.js}';

    protected $description = 'Create Vue Enums from PHP Enums';

    public function handle(): int
    {
        $path = $this->option('path');
        $output = $this->option('output');
        $namespace = 'App\\Enums';

        $content = '';

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace(
                [$path.DIRECTORY_SEPARATOR, '.php'],
                '',
                $file->getPathname(),
            );
            $className =
                $namespace.
                '\\'.
                str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

            if (! class_exists($className)) {
                require_once $file->getPathname();
            }

            if (! enum_exists($className)) {
                $this->warn("Skipping non-enum: $className");

                continue;
            }

            $enum = [];

            foreach ($className::cases() as $case) {
                $enum[$case->name] = $case->value;
            }

            $jsName = str_replace($namespace.'\\', '', $className);
            $jsName = str_replace('\\', '', $jsName);
            $content .=
                'export const '.
                $jsName.
                ' = '.
                json_encode($enum, JSON_PRETTY_PRINT).
                ";\n\n";

            $this->info('Creating '.$jsName.' enum');
        }

        file_put_contents($output, $content);
        $this->info('Enums created successfully');

        return self::SUCCESS;
    }
}
