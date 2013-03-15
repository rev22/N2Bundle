<?php

namespace Unislug\N2Bundle\View;

# use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Unislug\N2Bundle\Misc\SafeObject;
use Unislug\N2Bundle\Misc\Uri;

class Page extends SafeObject # implements ItemInterface
{
    public $item;
    public $content;
    
    public function asItem() { return $this->item; }
    
    public function template() { return "UnislugN2Bundle::page.html.twig"; }

    public function beget($i) {
        return new self($i);
    }
    
    public function buildView($i) {
        $v = $i->view;
        if (!isset($i->route)) {
            $this->content->buildRoute($i);
        }
        if ($v == null) return $this->beget($i);
        return $v;
    }

    public function root() {
       return $this->buildView($this->content->languageRoot());
    }
    
    public function cmsroot() {
       return $this->buildView($this->content->getRoot());
    }
    
    protected $children;
    public function children($type = null, $appendquery = null) {
        if (($type != null) || ($appendquery != null)) {
            $c = $this->content->getChildren($type, $appendquery);
            foreach ($c as &$i) $i = $this->buildView($i);
            return $c;
        }
        $c = $this->children; if ($c != null) return $c;
        $c = $this->content->getChildren();
        foreach ($c as &$i) $i = $this->buildView($i);
        return $this->children = $c;
    }

    protected $translations;
    public function getTranslations($itself = false) {
        $c = $this->translations; if ($c != null) return $c;
        $c = $this->content->getTranslations();
        $n = [];
        foreach ($c as $x) $n[$x->item->getLang()] = $x;
        foreach ($n as &$i) $i = $this->buildView($i->item);
        $l = $this->item->getLang();
        if ($l != null) {
            if ($itself) {
                $n[$this->item->getLang()] = $this;
            } else {
                unset($n[$this->item->getLang()]);
            }
        }
        return $this->translations = $n;
    }

    public function getNearestTranslation($l, &$leftover = []) {
        $c = $this->content->getTranslation($l, true, $leftover);
        if ($c == null) return null;
        return $this->buildView($c);
    }

    protected $languageroots;
    public function getLanguageRoots() {
        $c = $this->languageroots; if ($c != null) return $c;
        $c = [];
        $x = $this->cmsroot();
        $c[$x->getLang()] = $x;
        foreach ($x->children("LanguageRoot") as $y) $c[$y->getLang()] = $y;
        return $this->languageroots = $c;
    }
    private $availableSiteLanguages;
    public function getAvailableSiteLanguages() {
        $x = $this->availableSiteLanguages; if ($x != null) return $x;
        $x = array_keys($this->getLanguageRoots());
        $y = []; foreach ($x as $z) $y[$z] = $z;
        return $this->availableSiteLanguages = $y;
    }
    
    protected $siblings;
    public function siblings() {
        $c = $this->siblings;
        if ($c == null) {
            $c = $this->content->getSiblings();
            foreach ($c as &$i) $i = $this->buildView($i);
            $this->siblings = $c;
        }
        return $c;
    }

    protected $parents;
    public function parents() {
        $x = $this->parents;
        if ($x == null) {
            $x = $this->content->getCmsParents();
            foreach ($x as &$i) $i = $this->buildView($i->item);
            $this->parents = $x;
        }
        return $x;
    }
    protected $globalParents;
    public function getGlobalParents() {
        $x = $this->globalParents;
        if ($x == null) {
            $x = $this->content->getParents();
            foreach ($x as &$i) $i = $this->buildView($i->item);
            $this->globalParents = $x;
        }
        return $x;
    }
    public function getParent() {
        $x = $this->parents();
        return end($x);        
    }
    public function getGlobalParent() {
        $x = $this->getGlobalParents();
        return end($x);        
    }

    # public function p($x) {
    #     $x = $this->detail($x);
    #     if ($x == null) return "";
    #     return $x;
    # }

    public function respath($x = null) { return $this->content->respath($x); }
    public function cmspath($x = null) { return $this->content->cmspath($x); }
    public function cmsfullurl() { return $this->content->cmsfullurl(); }
    
    public function theImage() {
        $x = $this->detail("Image");
        if (isset($x)) {
            return "<img src=\"$x\">";
        }
        return "";
    }
    
    public function theText() {
        $x = $this->detail("Text");
        if (isset($x)) {
            return $x;
        }
        return "";
    }

    public function getId()     { return $this->item->getId(); }
    public function getIdName() { return $this->item->getId() . ":" . $this->item->getName(); }
    public function getLang() 	{ return $this->item->getLang(); }
    public function getTitle()  { return $this->item->getTitle(); }
    public function getType() { return $this->item->getType(); }
    
    public function theTitle($alt = "") {
        $x = $this->getTitle();
        if ($x != null) {
            return $x;
        }
        return $alt;
    }

    public function getUrl() { return $this->content->uri(); }
    public function getLocaleSwitchUrl($locale) { return Uri::gen($this->getUrl())->set("l", $locale); }
    
    public function vparams() {
        $body = "&lt; No page body &gt;";
        $title = "< Untitled >";
        $x = $this->item->getTitle();    if (isset($x)) $title  = $x;
        $x = $this->detail("Text");  if (isset($x)) $body   = $x; 
        return [ 'page' => $this, 'body' => $body, 'title' => $title ];
    }

    public function setup($request = null) { }
    
    // Setup and render model through the appropriate view
    public function render($controller) {
        $request = $controller->getRequest();
        if ($request->isMethod('POST')) {
            $this->setup($request); # Setup page with request on POST
        } else {
            $this->setup($request);
        }
        $params = $this->vparams();
        $params["front"] = $controller;
        return $controller->render($this->template(), $params);
    }

    public function __construct($i) {
        $item = $this->item 				  = $i->asItem();        
        $this->content 						  = $item->route;
        $item->view  					      = $this;
    }

    public static function create($i, $type = null, $prefix = "Unislug\\N2Bundle\\View\\") {
        if ($type == null) {
            $type = $i->asItem()->getType();
            if ($type == null) $type = "Page";
        }
        $c = $prefix . $type;
        return new $c($i);
    }
    public static function renderContent($c, $controller) { return (new self($c))->render($controller); }

    private $container;
    public function getContainer() {
        $x = $this->container;
        if ($x != null) return $x;
        return $this->container = $this->content->getContainer();
    }

    public function createFormBuilder($data = null, array $options = array())
    {
        return $this->getContainer()->get('form.factory')->createBuilder('form', $data, $options);
    }

    public function detail($n, $d = null) {
        return $this->item->getd($n, $d);
    }
}
