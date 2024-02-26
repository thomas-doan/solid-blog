<?php

namespace App\Container;

interface ContainerInterface {
    public function get($classEntity);
    public function has($classEntity);
}
