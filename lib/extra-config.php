#!/usr/bin/env php
<?php

/*
 * extra-config.php script
 *
 * parse to set extra configuration from $WORDPRESS_CONFIG_EXTRA
 * environment variable as the upstream Wordpress container
 * refuses to deal with these settings if the wp-config.php file
 * already exists (which is expected in case of wp-clone).
 *
 * the implementation here resembles the suggestion given,
 * additionally to make it safe, any changes are only applied if
 * no or starting *and* ending marker are found. A malformed
 * section will trigger to not edit the file.
 *
 * upstream: https://github.com/docker-library/wordpress/issues/333
 */

if (!isset($_ENV['WORDPRESS_CONFIG_EXTRA'])) {
    exit(0);
}

$lines = file('wp-config.php');

$out = fopen('php://memory', 'wb+');
if (!$out) {
    throw new UnexpectedValueException('Buffer failed');
}

# 0: start
# 1: end
# 2: non, printing, looking for end marker
$state = 0;
foreach ($lines as $index => $line) {
    switch ($state) {
        case 0:
            fwrite($out, $line);
            if (preg_match('(^/\*.*stop editing.*\*/$)', $line)) {
                $state = 1;
                if (rtrim($lines[$index+1]) === '// -- WORDPRESS_CONFIG_EXTRA BEGIN --') {
                    $state = 2;
                }
                fwrite($out, '// -- WORDPRESS_CONFIG_EXTRA BEGIN --'. "\n");
                fwrite($out, "//\n");
                fwrite($out, '// NOTE: Everything in this block will be overwritten by the'. "\n");
                fwrite($out, '//       WORDPRESS_CONFIG_EXTRA environment variable!'. "\n");
                fwrite($out, "//\n\n");
                fwrite($out, rtrim($_ENV['WORDPRESS_CONFIG_EXTRA']). "\n\n");
                fwrite($out, "//\n");
                fwrite($out, '// -- WORDPRESS_CONFIG_EXTRA END --'. "\n");
            }
            break;
        case 1:
            fwrite($out, $line);
            break;
        case 2:
            if (rtrim($line) === '// -- WORDPRESS_CONFIG_EXTRA END --') {
                $state = 1;
            }
            break;
        default:
            throw new UnexpectedValueException(sprintf('undefined state %d', $state));
    }
}

if ($state === 1) {
    rewind($out);
    file_put_contents('wp-config.php', $out);
    fclose($out);
}
else {
    fprintf(
        STDERR,
        "%s: can not apply extra config to wp-config.php\n",
        basename(__FILE__, '.php')
    );
}
