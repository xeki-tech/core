<?php

\xeki\module_manager::load_modules_url();

$match = \xeki\routes::process_actions();
$match = \xeki\routes::process_routes();

if ($_RUN_END_MODULES) $AG_MODULES->run_end();
if (is_array($_ARRAY_RUN_END))
    foreach ($_ARRAY_RUN_END as $item) {
        require_once "modules/$item/run_end.php";
    }
