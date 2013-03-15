<?php

namespace Unislug\N2Bundle\Router;

use Symfony\Component\DependencyInjection\ContainerAware;

class SlugRoute extends ContainerAware implements SlugRouteI {
    protected $route;
    # public $slug;
    # public $path; replaced by url() function
    
    /**
     * Locale used to select this route
     */
    public $locale;

    public $respath;
    public $baseurl;
    protected $container;
    protected $router;
    private $defroot;
    private $langroots;

    public function __construct($route, $container, $locale, $respath, $baseurl) {
        $this->route  	  = $route;
        $this->locale     = $locale;
        $this->respath    = rtrim($respath, "/");
        $this->baseurl    = $baseurl;
        $this->container  = $container;
        $this->router     = $container->get('router');
        if ($locale == null) {
            $this->defroot = $this;
            $this->langroots = [];
        }
    }
    
    public static function parse($path, $route, $container, $respath, $baseurl) {
        $reqpath = $path;
        $deflocale = $container->getParameter("locale");
        $locale = $deflocale;
        $cmspath = $reqpath;

        # Extract language slug
        $matches = [];
        $langrx = "|^([a-z]{2})(/+(.*))?$|";
        if (preg_match($langrx, $path, $matches)) {
            $locale = $matches[1];
            if (isset($matches[3])) {
                $cmspath = $matches[3];
            } else {
                $cmspath = "";
            }
        }
        
        # Remove initial path unless required for disambiguation
        if (($locale == $deflocale) && !preg_match($langrx, $cmspath)) {
            $locale = null;
        }

        # Normalize path
        $slug = trim($cmspath, "/");
        if ($slug == "") {
            $slugs = [];
        } else {
            $slugs = preg_split("|/+|", $slug);
            $slug = implode("/", $slugs);
        }
        
        # if ($cmspath != $slug) {
        #     $cmspath = $slug;
            
        #     # Recalculate path
        #     if (($locale == $deflocale) && !preg_match($langrx, $cmspath)) {
        #         $path = $cmspath;
        #     } else {
        #         $path = $locale . "/" . $cmspath;
        #     }
        # }
        # $slugroute = new SlugRoute($container, $_route, $slug, $path, $locale, $request->getBasePath());        
        # $container, $route, $slug, $path, $locale, $respath
        if ($locale == "") $locale = null;
        $root = new self($route, $container, $locale, $respath, $baseurl);

        $x = $root;
        foreach ($slugs as $s) {
            $x = $x->sub($s);
        }
        return $x;
    }

    public function path($slug = null) {
        if ($this->locale == null) {
            $l = "";
        } else {
            $l = $this->locale . "/";
        }
        if ($slug == null)         { return $l; }
        else if (is_array($slug))  { return $l . implode("/", $slug); }
        else         			   { return $l . $slug; }
    }

    private $baseuri;
    public function base() { return $this; }
    public function slugs() { return []; }
    public function uri($slug = null) {
        $x = $this->baseuri;
        if ($x == null) {
            $x = $this->baseuri = rtrim($this->router->generate($this->route, [ "path" => "" ]), "/") . "/";
        }
        return $x . $this->path($slug);
    }
    public function sub($slug) { return new Sub($this, $slug); }
    public function getContainer() { return $this->container; }
    public function defaultRoot() {
        $c = $this->defroot; if ($c != null) return $c;
        return $this->defroot = new self($this->route, $this->container, null, $this->respath, $this->baseurl);
    }
    public function languageRoot($locale) {
        $d = $this->defaultRoot();
        if ($locale == null) return $d;
        if (isset($d->langroots[$locale])) return $d->langroots[$locale];
        return $d->langroots[$locale] = new self($this->route, $this->container, $locale, $this->respath, $this->baseurl);
    }
    public function isRoot() { return true; }
}
