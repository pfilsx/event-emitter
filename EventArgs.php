<?php


namespace pfilsx\events;

/**
 * Class EventArgs - additional class for storing events arguments
 * @package pfilsx\events
 */
class EventArgs
{
    /**
     * @var mixed - Caller class instance
     */
    public $caller;

    /**
     * @var array - additional arguments passed via class::invoke method
     */
    public $args = [];

    /**
     * EventArgs constructor.
     * @param mixed $caller
     * @param array $args
     */
    public function __construct($caller, array $args = [])
    {
        $this->caller = $caller;
        $this->args = $args;
    }
}