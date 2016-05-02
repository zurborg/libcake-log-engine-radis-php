<?php

/**
 * @copyright 2016 David Zurborg <zurborg@cpan.org>
 * @link https://github.com/zurborg/libcake-log-engine-radis-php
 * @license https://opensource.org/licenses/ISC The ISC License
 */

namespace Cake\Log\Engine;

/**
 * Radis is Redis Radio
 */
class Radis extends BaseLog
{

    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_defaultConfig = [
        'server' => 'localhost:6379',
        'queue' => 'graylog-radis:queue',
        'levels' => [],
        'scopes' => [],
    ];

    /**
     * Used to map the string names to numeric constants
     *
     * @var array
     */
    protected $_levelMap = [
        'emergency' => 1,
        'alert'     => 2,
        'critical'  => 3,
        'error'     => 4,
        'warning'   => 5,
        'notice'    => 6,
        'info'      => 7,
        'debug'     => 8,
    ];

    /**
     * The system' hostname
     *
     * @var string
     */
    protected $_host;

    public function __construct()
    {
        parent::__construct();

        $config = $this->_config;

        $this->_host = gethostname();

        $redis = new \Redis();

        $server = array_key_exists('server', $config) ? $config['server'] : ':';

        list($host, $port) = explode(':', $server, 2);
        if (!$host) $host = 'localhost';
        if (!$port) $port = 6379;
        if (!$redis->pconnect($host, $port)) {
            die("cannot connect to $host:$port");
        }
        $this->_redis = $redis;
    }

    /**
     * Writes a message to redis
     * 
     * @param string $level The severity level of log you are making.
     * @param string $message The message you want to log.
     * @param array $context Additional information about the logged message
     * @return bool success of write.
     */
    public function log($level, $message, array $context = [])
    {
        $message = $this->_format($message, $context);

        if (!is_int($level))
            $level = $this->_levelMap[$level];

        $extras = [];

        foreach ($context as $key => $val)
        {
            if (is_null($val))
                continue;

            if (is_array($val) and !count($val))
                continue;

            if (is_string($val) and !strlen($val))
                continue;

            if ($key === 'scope')
            {
                if (count($val) === 1)
                {
                    $val = $val[0];
                }
                elseif (count($val) > 1)
                {
                    $key = 'scopes';
                }
            }

            if (substr($key, 0, 1) !== '_')
                $key = "_$key";

            $extras[$key] = $val;
        }

        $request = \Cake\Routing\Router::getRequest();
        if (!is_null($request)) {

            if (!is_null($request->session))
                $extras['_session_id'] = $request->session->id();

            $params = $request->params;
            if (!is_null($params) and is_array($params)) {
                foreach (['plugin', 'controller', 'action', 'prefix', 'bare'] as $key)
                {
                    if (array_key_exists($key, $params) and isset($params[$key]))
                        $extras["_cake_$key"] = $params[$key];
                }
            }

            $extras['_http_referer'] = $request->referer(false);
            $extras['_http_method'] = $request->method();
            $extras['_http_path'] = $request->here();
            $extras['_http_host'] = $request->host();
        }

        $gelf = array_merge($extras, [
            'host' => $this->_host,
            'timestamp' => sprintf('%0.06f', microtime(true)),
            'short_message' => $message,
            'level' => $level,
        ]);

        $gelf = json_encode($gelf);

        $config = $this->_config;
        $result = $this->_redis->lPush($config['queue'], $gelf);
        return !($result === false);
    }
}
