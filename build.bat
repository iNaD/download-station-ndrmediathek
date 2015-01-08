if not exist "build" mkdir "build"
if not exist "vendor" mkdir "vendor"

:: Delete old build data
del /s /f /q ".\build\*.*"

:: get dependencies
copy /Y "..\provider-repo\src\selfupdateable.php" ".\vendor\selfupdateable.php"
copy /Y "..\provider-repo\src\provider.php" ".\vendor\provider.php"

:: create the .host
7z a -ttar -so "ndrmediathek" "INFO" "ndrmediathek.php" "selfupdateable.php" | 7z a -si -tgzip "ndrmediathek.host"

move /Y ".\ndrmediathek.host" ".\build\ndrmediathek.host"

:: create the .tar.gz
7z a -ttar -so "ndrmediathek.tar" "ndrmediathek.php" "provider.php" | 7z a -si -tgzip "ndrmediathek.tar.gz"

move /Y ".\ndrmediathek.tar.gz" ".\build\ndrmediathek.tar.gz"

:: create md5 hashes
php -f "build.php"
