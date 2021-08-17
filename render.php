<?php
\xeki\module_manager::load_modules_url();

$match = \xeki\routes::process_actions();
$match = \xeki\routes::process_routes();
