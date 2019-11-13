<?php

namespace nailfor\queue\protocol;

interface Protocol 
{

    /** @return string */
    public function getProtocolIdentifierString();

    /** @return int */
    public function getProtocolVersion();
    
    public function setTopics($topics, $connection, $client);

    public function next($data);
    
}