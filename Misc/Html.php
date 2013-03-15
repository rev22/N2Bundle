<?php

namespace Unislug\N2Bundle\Misc;

# Quick html generator helper


class Html extends SafeObject {
    public function __get($v) { $c = (__NAMESPACE__ . "\\Html_") . $v; return new $c($v); }
}

class HtmlTag extends SafeObject {
    private $c;
    private $parameters;
    private $s;
    private static function flatten($x) {
        if (is_array($x)) {
            $l = [];
            foreach ($x as $y) {
                $y = self::flatten($y);
                $l = array_merge($l, $y);
            }
            return $l;
        }
        return [$x];
    }
    public function append($e) {
        $s = $this->c; if ($s == null) $s = [];
        $x = clone $this;
        if (is_array($e)) {
            foreach (self::flatten($e) as $y) $s[] = $y;
        } else {
            $s[] = $e;
        }
        $x->c = $s;
        return $x;
    }
    public function set($k, $v) {
        $x = clone $this;
        $x->s = null;
        $p = $x->parameters; if ($p == null) $p = [];
        $p[$k] = $v;
        $x->parameters = $p;
        return $x;
    }
    public function get($k) {
        $p = $this->parameters;
        if ($p == null) return null;
        if (array_has_key($k, $p)) return $p[$k];
        return null;
    }
    public function has($k) {
        $p = $this->parameters;
        if ($p == null) return false;
        return (array_has_key($k, $p));
    }
    public function addClass($c) {
        $x = clone $this;
        $x->s = null;
        $p = $x->parameters; if ($p == null) $p = [];
        $p[$k] = $v;
        $x->parameters = $p;
        return $x;
    }
    public function hasClass($c) {
        $p = $this->parameters;
        if ($p == null) return false;
        if (array_has_key("class", $p)) {
            $w = HtmlClass::gen($p["class"]);
            $this->parameters["class"] = $w;
            return $w->has($c);
        }
        return false;
    }
    public function params() {
        $p = $this->parameters;
        if ($p == null) return [];
        return $p;
    }
    public function appendText($x) {
        return $this->append(htmlspecialchars($x));
    }
    public function setTitle($t) { return $this->set("title", $t);}
    public function str() {
        $s = $this->s;
        if ($s != null) return $s;
        $c = $this->c;
        if ($c == null) $c = [ ];
        $n = $this->tagName();
        $p = $this->parameters;
        if ($p == null) {
            $p = "";
        } else {
            foreach ($p as $k => &$v) {
                $v = "$k" . "=" . "\"" . htmlspecialchars($v."") . "\"";
            }
            $p = implode(" ", $p);
            if ($p != "") $n .= " ";
        }
        $s = "<$n" . $p . ">" . implode("", $c) . "</$n>";
        return $this->s = $s;
    }
    public function __toString() { return $this->str(); }
    public function out() { echo $this->str(); }
}

class Html_a extends HtmlTag {
    public function tagName() { return "a"; }
    public function setHref($h) { return $this->set("href", $h); }
}

class Html_div extends HtmlTag {
    public function tagName() { return "div"; }
}

class Html_span extends HtmlTag {
    public function tagName() { return "span"; }
}

class HtmlClass extends SafeObject {
    private $s;
    private $c;
    public static function gen($q) {
        if ($q instanceof self) {
            return clone $q;
        } elseif (is_array($q)) {
            $x = new self;
            $x->c = $q;
            return $x;
        } else {
            $x = new self;
            $x->s = $q;
            return $x;
        }
    }
    public function arr() {
        if (isset($this->c)) return $this->c;
        $s = explode(" ", $this->s);
        $c = [];
        foreach ($s as $x) $c[$x] = "";
        return $this->c = $c;
    }
    public function get($k) {
        return array_key_exists($k, $this->arr());
    }
    public function add($k) {
        $m = $this->arr();
        $c[$k] = "";
        $n = clone $this;
        $n->c = $m;
        $n->s = null;
        return $n;
    }
    public function del($k) {
        $c = $this->arr();
        if (isset($c[$k])) {
            unset($c[$k]);
            if (empty($c)) return null;
            $n = clone $this;
            $n->c = $c;
            $n->s = null;
            return $n;
        } else {
            return $this;
        }
    }
    public function canonical() {
        $this->arr();
        unset($this->s);
        return $this;
    }
    public function str() {
        if (isset($this->s)) return $this->s;
        return $this->s = implode(" ", array_keys($this->arr()));
    }
    public function __toString() { return $this->str(); }
    public function out() { echo $this->str(); }
}
