:: Delete old data
del ndrmediathek.host

:: get recent version of the provider base class
copy /Y ..\provider-boilerplate\src\provider.php provider.php

:: create the .tar.gz
7z a -ttar -so ndrmediathek INFO ndrmediathek.php provider.php | 7z a -si -tgzip ndrmediathek.host

del provider.php