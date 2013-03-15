<?php

namespace Unislug\N2Bundle\Misc\LabjsRequire;

use Unislug\N2Bundle\Misc\SafeObject;

class LabjsRequire extends SafeObject {
    public $libs;
    public $required;
    public function __construct() {
        $this->libs = [];
        $this->required = [];
    }
    public function snippet($name, $snip, $deps = []) {
        new Snip($this, $name, $snip, $deps);
        return $this;
    }
    public function source($name, $src, $deps = []) {
        new Src($this, $name, $src, $deps);
        return $this;
    }
    public function ask($x) {
        if (is_array($x)) {
            foreach ($x as $y) $this->required[$y] = "";
        } else {
            $this->required[$x] = "";
        }
        return $this;
    }
    public $chain;
    public function out() {
        $c = new LoadChain($this);
        foreach (array_keys($this->required) as $y) $c->add($y);
        $r = $c->out();
        $this->chain = $c;
        # Invalidate previous state of the object
        $this->libs = null;
        $this->required = null;
        return "";
    }
    public function asked($x) {
        return $this->chain->used($x);
    }
}

class Lib extends SafeObject {
    public $nym;
    public $dep;
    public $usr;
    public $db;
    public function __construct($db, $name, $deps = []) {
        $this->nym  = $name;
        $this->dep  = [];
        $this->db   = $db;
        $db->libs[$name] = $this;
        $this->requires($deps);
    }
    public function requires($x) {
        if (is_array($x)) {
            foreach ($x as $y) $this->requires($y);
            return $this;
        }
        if (isset($x->nym)) {
            $dep = $x;
            $nym = $x->nym;
        } else {
            $dep = $this->db->libs[$x];
            $nym = $x;
        }
        $this->dep[$nym] = $dep;
        return $this;
    }
}

class Src extends Lib {
    public $src;
    public function __construct($db, $name, $src, $deps = []) {
        $this->src = $src;
        parent::__construct($db, $name, $deps);
    }
}

class Snip extends Lib {
    public $snip;
    public function __construct($db, $name, $snip, $deps = []) {
        $this->snip = $snip;
        parent::__construct($db, $name, $deps);
    }
}

class LoadChain extends SafeObject {
    private $db;
    private $chain;
    private $levels;
    public function __construct($db) {
        $this->db = $db;
        $this->chain = [];
        $this->levels = [];
    }
    public function add($x) {
        if (is_string($x)) $x = $this->db->libs[$x];
        $n = $x->nym;
        $dep = $x->dep;
        # throw new \Exception($n . "::" . var_dump(implode("xx", $dep)));
        $l = 0;
        if (isset($this->levels[$n])) return $this->levels[$n];
        foreach (array_keys($dep) as $a) {
            $r = $this->add($a);
            if ($r >= $l) $l = $r+1;
        }
        if ($l >= count($this->chain)) $this->chain[$l] = [ 0 => [], 1 => [] ];
        # print "Adding $n at level $l\n";
        $this->chain[$l][($x instanceof Src) ? 0 : 1][$n] = $x;
        return $this->levels[$n] = $l;
    }
    public function out() {
        print "<script type=\"text/javascript\" >\n";
        require "LAB.js";
        $w = false;
        foreach ($this->chain as $b) {
            if ($w) {
                if ($b[1]) {
                    print "\n.wait(function(){\n";
                    foreach ($b[1] as $x) {
                        $x = $x->snip;
                        print "(function(){$x})();\n";
                    }
                    print "})";
                } else {
                    print ".wait()";
                }
            } else {
                foreach ($b[1] as $x) {
                    $x = $x->snip;
                    print "(function(){$x})();\n";
                }
                print '$LAB = $LAB';
            }
            foreach ($b[0] as $x) {
                print ".script('" . $x->src . "')";
            }
            $w = true;
        }
        print(";\n</script>");
    }
    public function used($x) {
        return array_key_exists($x, $this->levels);
    }
}
