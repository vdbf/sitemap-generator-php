<?php include dirname(__DIR__) . '/vendor/autoload.php';

use Vdbf\SiteMapper\Console\GenerateMappingApplication;

$app = new GenerateMappingApplication();

$app->run();

