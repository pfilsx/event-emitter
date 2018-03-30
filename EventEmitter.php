<?php


namespace pfilsx\events;

/**
 * Class EventEmitter - simple event emitter system on PHP
 * @package pfilsx\events
 */
class EventEmitter
{
    protected static $_events = [];

    protected $_instance_events = [];

    /**
     * Returns fully qualified class name
     * @return string
     */
    public static final function className()
    {
        return get_called_class();
    }

    /**
     * Add an event handler
     * @param string $event - event name to handle
     * @param callable $handler - handler
     * @throws \Exception - invalid args exception
     */
    public static final function addEventHandler($event, callable $handler){
        if (!is_string($event)){
            throw new \Exception("Invalid argument passed. Event name must be a string");
        }
        if (!isset(static::$_events[static::className()])){
            static::$_events[static::className()] = [];
        }
        if (!array_key_exists($event, static::$_events[static::className()])){
            static::$_events[static::className()][$event] = [];
        }
        static::$_events[static::className()][$event][] = $handler;
    }
    /**
     * Add an event handler for specific instance
     * @param string $event - event name to handle
     * @param callable $handler - handler
     * @throws \Exception - invalid args exception
     */
    public final function addInstanceEventHandler($event, callable $handler){
        if (!is_string($event)){
            throw new \Exception("Invalid argument passed. Event name must be a string");
        }
        if (!array_key_exists($event, $this->_instance_events)){
            $this->_instance_events[$event] = [];
        }
        $this->_instance_events[$event][] = $handler;
    }

    /**
     * Remove an event handler
     * @param string $event - event name to remove handler
     * @param callable $handler - handler
     * @throws \Exception - invalid args exception
     */
    public static final function removeEventHandler($event, callable $handler){
        if (!is_string($event)){
            throw new \Exception("Invalid argument passed. Event name must be a string");
        }
        if (!isset(static::$_events[static::className()])){
            static::$_events[static::className()] = [];
        }
        if (!array_key_exists($event, static::$_events[static::className()])){
            return;
        }
        if (($index = array_search($handler, static::$_events[static::className()][$event])) !== false){
            unset(static::$_events[static::className()][$event][$index]);
        }
    }
    /**
     * Remove an event handler for specific instance
     * @param string $event - event name to remove handler
     * @param callable $handler - handler
     * @throws \Exception - invalid args exception
     */
    public final function removeInstanceEventHandler($event, callable $handler){
        if (!is_string($event)){
            throw new \Exception("Invalid argument passed. Event name must be a string");
        }
        if (!array_key_exists($event, $this->_instance_events)){
            return;
        }
        if (($index = array_search($handler, $this->_instance_events[$event])) !== false){
            unset($this->_instance_events[$event][$index]);
        }
    }
    /**
     * Removes all event handlers for specific event or for all events
     * @param null||string $event - event name or null for all events
     * @throws \Exception - invalid args exception
     */
    public static final function removeAllEventHandlers($event = null){
        if ($event == null || !isset(static::$_events[static::className()])){
            static::$_events[static::className()] = [];
            return;
        }
        if (!is_string($event)){
            throw new \Exception("Invalid argument passed. Event name must be a string or null");
        }
        unset(static::$_events[static::className()][$event]);
    }
    /**
     * Removes all event handlers for specific event or for all events for specific instance
     * @param null||string $event - event name or null for all events
     * @throws \Exception - invalid args exception
     */
    public final function removeAllInstanceEventHandlers($event = null)
    {
        if ($event == null){
            $this->_instance_events = [];
            return;
        }
        if (!is_string($event)){
            throw new \Exception("Invalid argument passed. Event name must be a string or null");
        }
        unset($this->_instance_events[$event]);
    }

    /**
     * Invoke a specific event by name
     * @param string $event - event name to invoke
     * @param array $args - array of arguments
     * @throws \Exception - invalid args exception
     */
    protected final function invoke($event, array $args = []){
        if (!is_string($event)){
            throw new \Exception("Invalid argument passed. Event name must be a string or null");
        }
        if (!isset(static::$_events[static::className()])){
            static::$_events[static::className()] = [];
        }
        $classHandlers = array_key_exists($event, static::$_events[static::className()]);
        $instanceHandlers = array_key_exists($event, $this->_instance_events);
        if (!$classHandlers && !$instanceHandlers){
            return;
        }
        $args = new EventArgs($this, $args);
        if ($classHandlers){
            foreach (static::$_events[static::className()][$event] as $handler){
                call_user_func($handler, $args);
            }
        }
        if ($instanceHandlers){
            foreach ($this->_instance_events[$event] as $handler){
                call_user_func($handler, $args);
            }
        }
    }

    /**
     * Return list of specified events for class with their handlers
     * @return array
     */
    public static final function getEvents(){
        if (!isset(static::$_events[static::className()])){
            static::$_events[static::className()] = [];
        }
        return static::$_events[static::className()];
    }
    /**
     * Return list of specified events for specific instance with their handlers
     * @return array
     */
    public final function getInstanceEvents(){
        return $this->_instance_events;
    }
}