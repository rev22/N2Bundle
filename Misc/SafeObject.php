<?php

namespace Unislug\N2Bundle\Misc;

class SafeObject {
    public function __set($name, $value) {
        throw new \Exception("Create a dynamic field $name");
    }
}
