<?php

namespace nailfor\queue\protocol;

interface IProtocol 
{

    /** @return string */
    public function getProtocolIdentifierString();

    /** @return int */
    public function getProtocolVersion();
    
    public function next($data);
    
    /**
     * Return addition listeners of protocol
     * @return array
     */
    public function getListeners(): array;
}