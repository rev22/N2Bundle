<?php

namespace Unislug\N2Bundle\Edit;

use Unislug\N2Bundle\Content\Content;

# Base class for manipulating unique global objects

class Globals extends EditPage
{
    protected $request;
    public function __construct() {}
    public static function loadup($i, $manager, $route, $request, $opts = null) {
        if ($i == null) $i = new self();
        $i->request = $request;
        if ($i->content != null) return; # Somembody already set up the content for us
        if (!$opts) {
            $t = $i->getType();
            if ($t != null) $opts = [ 'type' => $t ];
        }
        $c = Content::retrieve($manager, $route, $opts);
        if ($c != null) {
            # This mimicks the parent constructor
            $item = $i->item  = $c->asItem();
            $i->content 	  = $item->route;
            $item->view  	  = $i;
        } else {
            throw new \Exception("Could not obtain object from database!");
        }
        $i->setupObject($request);
        return $i;
    }
    # public function beget($i) { return new self($i); }
    public function canBeRenamed() { return false; }
    public function hasFixedLocation() { return true; }
    public function hasVisibility() { return false; }
    
    public function getSiblingTypes() { return []; }
    public function name() { return "globals"; }

    static $entities = [
        "home"  		 => "Unislug\\N2Bundle\\Edit\\StartPage",
        "languages"  	 => "Unislug\\N2Bundle\\Edit\\LanguageRoot",
        "languageparts"  => "Unislug\\N2Bundle\\Edit\\LanguageParts"
        ];
    
    public static function authorGlobal($entity, $manager, $route, $request, $controller) {
        $i = self::$entities;
        $p = $i[$entity];
        if ($p == null) throw new \Exception("Could not initialize for object: $entity");
        $x = $p::loadup(null, $manager, $route, $request);
        $x->entity_name = $entity;
        $x->getBuiltForm();
        return $x->render($controller);
    }

    protected $entity_name;
    public function getAction() { return "edit-" . $this->entity_name; }
}
