<?php

namespace Unislug\N2Bundle\Edit;

use StartPage;

class LanguageRoot extends Globals
{
    public function getType() { return "StartPage"; }

    public static function loadup($i, $manager, $route, $request, $opts = null) {
        if ($i == null) $i = new self();
        return parent::loadup($i, $manager, $route->base(), $request);
    }

    # public static function authorGlobal($manager, $route, $request, $controller) { return self::loadup(null, $manager, $route, $request)->render($controller); }
    public function hasFixedLocation() { return true; }

    public function getText() { return $this->detail("Text"); }
    #### public function setText($x) { return $this->item->setd("Text", $x); }

    public function render($x) {
        return parent::render($x);
    }

    public function getTransCandidates() {
        $x = $this->getLanguageRoots();
        # foreach ($x as $y) { if (!($y instanceof Page)) throw new \Exception("aha" . get_class($y)); }
        return $x;
    }
}
