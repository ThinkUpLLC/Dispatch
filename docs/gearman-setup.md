Setting up Gearman for Crawl Manager On OSX
-----------------------------------------------
NOTE: Be sure you have the latest XCode and XCode command line tools installed.

Install homebrew:

    mkdir homebrew && curl -L https://github.com/mxcl/homebrew/tarball/master | tar xz --strip 1 -C homebrew

Install homebrew gearman:

   ./homebrew/bin/brew install gearman

Install pear if needed:

    curl -O http://pear.php.net/go-pear.phar; sudo php -d detect_unicode=0 go-pear.phar 
    See more at: http://jason.pureconcepts.net/2012/10/install-pear-pecl-mac-os-x/#sthash.meMYqY4A.dpuf

Install homebrew autoconf

    ./bin/brew install autoconf

Install gearman php pecl extension:

    download http://pecl.php.net/get/gearman 

    untar and cd into the gearman directory, and run: 

       phpize
      ./configure --with-gearman=/path/to/homebrew
       make
       make test
       make install

    add 'extension=gearman.so' to your php.ini
    
    run:  php --info | grep "gearman support"
    it should output "gearman support => enabled"

Setting up Gearman for Crawl Manager On Debian
-----------------------------------------------
????



