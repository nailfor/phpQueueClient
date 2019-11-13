<?php

namespace nailfor\queue\protocol;

use Exception;

/**
 * The client received a packet of data that violates the AMQP protocol.
 */
final class Violation extends Exception {}
