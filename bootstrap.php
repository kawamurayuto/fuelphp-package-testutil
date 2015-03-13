<?php

Autoloader::add_core_namespace('TestUtil');

Autoloader::add_classes(array(
    'TestUtil\\DbTestCase' => __DIR__ . '/classes/testutil/dbtestcase.php',
    'TestUtil\\OrmModelTestCase' => __DIR__ . '/classes/testutil/ormmodeltestcase.php',
    'Profiler' => __DIR__ . '/classes/profiler.php',
));
