<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="./tests/support/bootstrap.php"
    colors="true"
    verbose="true">
    <testsuites>
        <testsuite name="<?php echo $module['name']; ?> Test Suite">
            <directory>./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>

