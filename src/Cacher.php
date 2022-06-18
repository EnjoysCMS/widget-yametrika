<?php

declare(strict_types=1);

namespace EnjoysCMS\WidgetYaMetrika;

use Enjoys\SimpleCache\Cacher\FileCache;

return new FileCache([
    'path' => $_ENV['TEMP_DIR'] . '/cache/yametrika-widgets'
]);
