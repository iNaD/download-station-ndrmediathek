:: Delete old data
del ndrmediathek.tar.gz ndrmediathek.php.md5
:: create the .tar.gz
7z a -ttar -so ndrmediathek.tar ndrmediathek.php provider.php | 7z a -si -tgzip ndrmediathek.tar.gz

:: create md5 hashes
php -f build.php
