Cake\Log\Engine\Radis
===============

Radis is Redis Radio




* Class name: Radis
* Namespace: Cake\Log\Engine
* Parent class: Cake\Log\Engine\BaseLog





Properties
----------


### $_defaultConfig

    protected array $_defaultConfig = array('server' => 'localhost:6379', 'queue' => 'graylog-radis:queue', 'levels' => array(), 'scopes' => array())

Default config for this class



* Visibility: **protected**


### $_levelMap

    protected array $_levelMap = array('emergency' => 1, 'alert' => 2, 'critical' => 3, 'error' => 4, 'warning' => 5, 'notice' => 6, 'info' => 7, 'debug' => 8)

Used to map the string names to numeric constants



* Visibility: **protected**


### $_host

    protected string $_host

The system' hostname



* Visibility: **protected**


Methods
-------


### __construct

    mixed Cake\Log\Engine\Radis::__construct()





* Visibility: **public**




### log

    boolean Cake\Log\Engine\Radis::log(string $level, string $message, array $context)

Writes a message to redis



* Visibility: **public**


#### Arguments
* $level **string** - &lt;p&gt;The severity level of log you are making.&lt;/p&gt;
* $message **string** - &lt;p&gt;The message you want to log.&lt;/p&gt;
* $context **array** - &lt;p&gt;Additional information about the logged message&lt;/p&gt;


