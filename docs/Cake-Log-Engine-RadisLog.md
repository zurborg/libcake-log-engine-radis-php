Cake\Log\Engine\RadisLog
===============

Radis is Redis Radio




* Class name: RadisLog
* Namespace: Cake\Log\Engine
* Parent class: Cake\Log\Engine\BaseLog







Methods
-------


### __construct

    mixed Cake\Log\Engine\RadisLog::__construct(array<mixed,mixed> $config)

Constructor



* Visibility: **public**


#### Arguments
* $config **array&lt;mixed,mixed&gt;**



### log

    boolean Cake\Log\Engine\RadisLog::log(string $level, string $message, array $context)

Logs a message through Redis



* Visibility: **public**


#### Arguments
* $level **string** - &lt;p&gt;The severity level of log you are making.&lt;/p&gt;
* $message **string** - &lt;p&gt;The message you want to log.&lt;/p&gt;
* $context **array** - &lt;p&gt;Additional information about the logged message&lt;/p&gt;


