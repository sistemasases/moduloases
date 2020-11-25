<?php

require_once( __DIR__ . "/../module_loader.php" );

module_loader( "periods" );

echo "Test: core_periods_get_current_period()<br>";
echo "Result: ";
print_r( core_periods_get_current_period() );

?>