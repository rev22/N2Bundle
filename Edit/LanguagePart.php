<?php

namespace Unislug\N2Bundle\Edit;

class LanguagePart extends Globals
{
    public function getType() { return "LanguagesPart"; }

    public function getChildren() { return []; }
    
    public static function loadup($i, $manager, $route, $request, $opts = null) {
        if ($i == null) $i = new self();
        return parent::loadup($i, $manager, $route->base(), $request);
    }

    public static function authorGlobal($manager, $route, $request, $controller) {
        return self::loadup(null, $manager, $route, $request)->render($controller);
    }
    public function hasFixedLocation() { return true; }
}
