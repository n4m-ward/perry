<?php

namespace Perry\ControllerWrapper;

use Perry\SwaggerGenerator\SwaggerGenerator;

/**
 * @template T
 */
class ControllerWrapper
{
    /**
     * @var T $controller
     */
    protected $controller;

    private SwaggerGenerator $generator;


    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->generator = app(SwaggerGenerator::class);
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
            $this->generator->generateDocAndSaveOnCache($args, $response);

            return $response;
        } catch (\Throwable $e) {

            throw $e;
        }
    }
}