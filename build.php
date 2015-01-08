<?php

file_put_contents('build/ndrmediathek.php.md5', md5_file('ndrmediathek.php'));
file_put_contents('build/ndrmediathek.tar.gz.md5', md5_file('ndrmediathek.tar.gz'));
file_put_contents('build/ndrmediathek.json', json_encode([
    'name'      => 'ndrmediathek',
    'filename'  => 'ndrmediathek.tar.gz',
    'md5'       => [
        'provider'  => file_get_contents('build/ndrmediathek.php.md5'),
        'archive'  => file_get_contents('build/ndrmediathek.tar.gz.md5'),
    ],
    'updatedAt' => date("r")
]));
