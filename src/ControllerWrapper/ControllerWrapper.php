<?php

namespace Perry\ControllerWrapper;

/**
 * @template T
 */
class ControllerWrapper
{
    /**
     * @var T $controller
     */
    protected $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param T $controller
     * @return T|ControllerWrapper
     */
    public static function wrap($controller)
    {
        return new ControllerWrapper($controller);
    }

    /**
     * @throws \Throwable
     */
    public function __call($method, $args)
    {
        try {
            $response = call_user_func_array([$this->controller, $method], $args);

            return $response;
        } catch (\Throwable $e) {

            throw $e;
        }
    }
}