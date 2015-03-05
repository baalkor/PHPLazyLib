# PHPLazyLib

PHP Lazy Lib are a set of small PHP libraries written to facilitate tasks.

## Description

### Configuration File parser

This small subset contains class that allow manipulation of ini file. In some
case you can event extends ConfigurationFile to suit your file format.

At this time, only INI file are testes.

Use example : 

File : config.ini
`
[DATABSES]
user="root"

[DATABASE#1]
sid="testdb"
read="true"
port=3306

[TYPED_TEST]
bool_1="false"
bool_2="true"
str="test_Stru"
int="10"
float="10.36"
`
`
 $cfg = new INIConfigurationFile("config.ini");
 echo $cfg->get("DATABASE#1", "sid"); // => will print "testdb"
 `
//Support types 
 var_dump($cfg->get("TYPED_TEST", "float")); // => will print float(10.36) 
 var_dump($cfg->get("TYPED_TEST", "bool_1")); // => will print bool(false) 
 var_dump($cfg->get("TYPED_TEST", "int")); // => will print int(10) 

//Adding new elements
 
 $cfg->addSection("TEST");
 $cdg->set("TEST", "testparam", 10);

 // Getting whole metasection
 $cfg->getSection("TEST"); 

// Write modification to disks
$cfg->writeConfig($overwrite); // By default, it will overwrite current config
                               // but if sets to true, it will write it 
                              // like config.ini-<unix timestamps>

`
### Contributions
As the project is quite new every contribution or comment will be appreciated.

Baalkor

 - ConfigurationFile : Use to read/write parameters 