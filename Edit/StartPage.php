<?php

namespace Unislug\N2Bundle\Edit;

use Unislug\N2Bundle\View\Page as Page;

class StartPage extends Globals
{
    public function getType() { return "StartPage"; }
    public function getChildren() { return []; }
    
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

    public function hasLargeText()        { return true; }

    public function getTranslations($itself = false) {
        $x = $this->getLanguageRoots();
        $lang = $this->item->getLang();
        if ($lang != null) {
            if ($itself) {
                $x[$lang] = $this;
            } else {
                unset($x[$lang]);
            }
        }
        # foreach ($x as $y) { if (!($y instanceof Page)) throw new \Exception("aha" . get_class($y)); }
        return $x;
    }
    public function getTransCanditates() {
        return$this->getTranslations();
    }
    
    public function buildForm($b) {
        $b = parent::buildForm($b);
        $this->registerScript("jquery", "//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js");
        $this->registerScript("tinymce", $this->respath("/tinymce/jscripts/tiny_mce/tiny_mce.js"));
        # $this->registerScript("tinymce", "http://moxiecode.cachefly.net/tinymce/v6/js/all.min.js");
        $this->registerSnippet("tinymceload", "tinyMCE.init({theme:\"advanced\",mode:\"specific_textareas\",editor_selector:\"tinymce\",height:\"400\"});");
        $b->add("Text", "textarea", [ 'attr' => [ 'class' => 'tinymce', 'style' => 'width:100%;min-height:400px;height:80%' ] ]);
        return $b;
    }
}
