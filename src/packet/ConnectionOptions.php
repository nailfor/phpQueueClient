<?php
namespace nailfor\queue\packet;

use nailfor\queue\packet\QoS\Levels;

class ConnectionOptions
{
    /**
     * Username
     *
     * Can be used by broker for authentication
     * and authorisation
     *
     * @var string
     */
    protected $options;

    /**
     * ConnectionOptions constructor.
     *
     * @param array $options [optional]
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function __get(string $name)
    {
        return $this->options[$name] ?? null;
    }
}
