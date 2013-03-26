openlss/lib-tml
=======

Library for parsing and creating our Tabbed Meta Lanuage (TML)

TML is a simple storage language that LSS can use. TML stands for “Tabbed Meta Language” while tabs arent required as a delimiter they are suggested.

Syntax
----
  * Each level must be indented by one delimiter (TAB is the default)
  * Name value pairs must be separated by one delimiter (TAB again)
  * Empty arrays must be defined as: name []
  * Anonymous array entries can be added to lists but when converted back to TML they will show with the indexes
  * All TML files require a root element that defines the document

Example
----
```
app
        source  /opt/myapp
        mirror  /data/mirror
        blankarray    []
        packages
                usr/lib/news
                        version         0.0.1
                        description     The news library
                        depends
                                main/util/func  0.0.1
                        manifest
                                lib/news.php
                usr/app-web/news
                        version         0.0.1
                        description     News front end
                        depends
                                main/sys/db     0.0.1
                                usr/lib/news    0.0.1
                        manifest
                                ctl/news.php
                                tpl/news.tpl.php
                                news.txt
```


Usage
----
```php
use \LSS\TML

//setup our test array
$array = array('test'=>'test1','test2'=>'test3');

//conert array to TML
$tml = TML::fromArray($array);

//convert TML to array
$array = TML::toArray($tml);
```

Reference
----

### (string) TML::fromArray($arr,$level=0,$newline=true)
  * $arr		The array to be parsed into TML
  * $level		This is an internal pointer for nested parsing
  * $newline	This is an internal flag for nested parsing
Returns TML that can be transported as texted and is excellent for compression

### (array) TML::toArray($buf)
  * $buf		The TML to be parsed
Returns an array matching the original input array

