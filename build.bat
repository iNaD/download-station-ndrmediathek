:: Delete old data
del ndrmediathek.host
:: create the .tar.gz
7z a -ttar -so ndrmediathek INFO ndrmediathek.php | 7z a -si -tgzip ndrmediathek.host
