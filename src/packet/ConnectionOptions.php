<?php
namespace nailfor\queue\packet;

class ConnectionOptions
{
    use Traits\HasAttributes;
    use Traits\Stringable;

    /**
     * ConnectionOptions constructor.
     *
     * @param array $options [optional]
     */
    public function __construct(array $options = [])
    {
        $this->attributes = $options;
    }
}