<?php

namespace Unislug\N2Bundle\Router;

class Sub implements SlugRouteI {
    protected $route;
    protected $sub;
    protected $base;

    public function __construct($route, $sub) {
        $this->route = $route;
        $this->sub = $sub;
        $this->base = $route->base();
    }

    public function base() { return $this->base; }
    public function slugs() {
        $p = [];
        $x = $this;

        while (isset($x->sub)) {
            $p[] = $x->sub;
            $x = $x->route;
        }

        return array_reverse($p);
    }

    public function path($slug = null) {
        $p = $this->slugs();

        if ($slug == null)  	  { }
        elseif (is_array($slug))  { $p = array_merge($p, $slug); }
        else  				      { $p[] = $slug; }

        return $this->base->path($p);
    }
   
    public function uri($slug = null) {
        $p = $this->slugs();

        if ($slug == null)  	  { }
        elseif (is_array($slug))  { $p = array_merge($p, $slug); }
        else  				      { $p[] = $slug; }

        return $this->base->uri($p);
    }

    public function sub($slug) { return new Sub($this, $slug); }

    public function isRoot() { return false; }
}
