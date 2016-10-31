<?php

/**
 * Radis log engine for CakePHP
 * @copyright 2016 David Zurborg <zurborg@cpan.org>
 * @link https://github.com/zurborg/libcake-log-engine-radis-php
 * @license https://opensource.org/licenses/ISC The ISC License
 */

namespace Cake\Log\Engine;

/**
 * Radis is Redis Radio
 */
class RadisLog extends BaseLog
{

    /**
     * Default config for this class
     * 
     * @var mixed[]
     */
    protected $_defaultConfig = [
        'server' => 'localhost:6379',
        'queue' => 'graylog-radis:queue',
        'redis_mock' => null,
        'levels' => [],
        'scopes' => [],
    ];

    /**
     * Instance of Radis
     * 
     * @var \Log\Radis
     */
    protected $radis;

    /**
     * Constructor
     * 
     * @param mixed[] $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $config = $this->_config;

        $radis = new \Log\Radis($config['server'], $config['queue'], $config['redis_mock']);

        $this->radis = $radis;
    }

    /**
     * Logs a message through Redis
     * 
     * @param string $level The severity level of log you are making.
     * @param string $message The message you want to log.
     * @param array $context Additional information about the logged message
     * @return bool success of write.
     */
    public function log($level, $message, array $context = [])
    {
        $message = $this->_format($message, $context);

        foreach ($context as $key => $val)
        {
            if ($key === 'scope')
            {
                unset($context['scope']);
                if (count($val) === 1)
                {
                    $context['scope'] = $val[0];
                }
                elseif (count($val) > 1)
                {
                    $context['scopes'] = $val;
                }
            }
        }

        $context['cake_application'] = \Cake\Core\Configure::read('App.name');

        $request = \Cake\Routing\Router::getRequest();
        if (!is_null($request)) {

            if (!is_null($request->session))
                $context['cake_session'] = $request->session->id();

            $params = $request->params;
            if (!is_null($params) and is_array($params)) {
                foreach (['plugin', 'controller', 'action', 'prefix', 'bare'] as $key)
                {
                    if (array_key_exists($key, $params) and isset($params[$key]))
                        $context["cake_$key"] = $params[$key];
                }
            }
        }

        return $this->radis->log($level, $message, $context);
    }
}
