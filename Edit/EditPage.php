<?php

namespace Unislug\N2Bundle\Edit;
use Unislug\N2Bundle\View\Page;

use Unislug\N2Bundle\Misc\LabjsRequire\LabjsRequire;
use Unislug\N2Bundle\Misc\Uri;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class EditPage extends Page # implements ItemInterface
{
    private $template;
    public function template() {
        if (isset($this->template)) {
            return $this->template;
        } else {
            return ;
        }
    }

    public function beget($i) {
        $x = new self($i);
        $x->page_theme = $this->page_theme;
        return $x;
    }
    
    public function getName()   { return $this->item->getName(); }    public function setName($x)   { return $this->item->setName($x); }
    public function getTitle()  { return $this->item->getTitle(); }   public function setTitle($x)  { return $this->item->setTitle($x); }
    public function getType()   { return $this->item->getType(); }    public function setType($x)   { return $this->item->setType($x); }

    public function actionName() { return "edit"; }
    public function theAction() { return "Modifica"; }
    public function getViewUrl() {
        if ($this->versionof != null) return;
        return parent::getUrl();
    }
    protected $page_theme;
    public function getUrlData() {
        $q = [ "action" => $this->actionName() ];
        if ($this->versionof != null) $q["verid"] = $this->getId();
        if ($this->page_theme != null) $q["theme"] = $this->page_theme;
        return $q;
    }
    public function getUrl() {
        return Uri::gen(parent::getUrl(), http_build_query($this->getUrlData()));
    }

    public function build_fancy_edit_form($b) {
        // $dateattr = [ 'style' => 'display:inline-block; padding: 1em' ];
        # $b ->add('title',      'text',        [ 'label'    => 'Titolo: ', 'attr' => [ 'style' => 'font-size: 120%' ] ])
        # $b->add('name',  	    'text',  	   [ 'label'    => 'Slug: ' ])
        $b->add('visible',    'checkbox',    [ 'label'    => 'Visibile: ' ]);
        # ->add('created',    'datetime',    [ 'label'    => 'Creato il: ',  	   'required' => false, 'widget' => 'single_text' ])
        # ->add('published',  'datetime',    [ 'label'    => 'Pubblicato il: ',  'required' => false, 'widget' => 'single_text' ])
        # ->add('updated',    'datetime',    [ 'label'    => 'Aggiornato il: ',  'required' => false, 'widget' => 'single_text' ])
        # ->add('expires',    'datetime',    [ 'label'    => 'Scade il: ',       'required' => false, 'widget' => 'single_text' ])
        $b->add('visible',    'checkbox',    [ 'label'    => 'Visible: ' ]);
        $b->add('type',  	    'choice',      [ 'choices'  => [ 'preset' => $i->getType() ], 'required' => true, 'label' => 'Type: ' ]);
        # ->add('details',    'collection',  [
        #           'type' => 'detail',
        #           'options' => [
        #               'required'  => false,
        #               'attr'      => [ 'class' => 'email-box' ]
        #               ]
        #           ])t
        return $b;
    }

    public function isVisible() { return $this->item->getVisible(); }
    public function compatibleTypes() { return [ $this->getType() ]; }

    public function getSiblingType() { return $this->getType(); }
    public function setSiblingType($x) { }
    public function getSiblingTypes() { return [ $this->getType() =>  $this->getType() ]; }
    public function setSiblingTypes($x) { }

    public function getParentTypes() { }

    public function getVersion() { $v = $this->versionof; if ($v != null) return $this->getId(); return; }
    # public function setVersion($x) { }

    private $listVersions;
    public function listVersions() {
        $x = $this->listVersions; if ($x != null) return $x;
        $versions = $this->getVersions();
        $l = [];
        $detailed = "Y-m-d h:i:s";

        foreach ($versions as $v) {
            $u = $v->item->getUpdated();
            if ($u == null) continue;
            $date = $u->format("Y-m-d");
            if (isset($l[$date])) {
                $p = $l[$date];
                if ($p !== 0) {
                    $l[$date] = 0;
                    $l[$p->item->getUpdated()->format($detailed)] = $p;
                }
                $l[$u->format($detailed)] = $v;
            } else {
                $l[$date] = $v;
            }
        }
        foreach ($l as $x => $y) {
            if ($y === 0) unset($l[$x]);
        }

        // throw new \Exception(var_dump($l));
        
        asort($l);
        $l = array_reverse($l);

        $r = [];
        $n = 0;
        foreach ($l as $x => $y) $r[$y->getUrl().""] = $x;
        return $this->listVersions = $r;
    }

    public function getChildren() {
        return [];
    }

    private $transCandidates;
    public function getTransCandidates() {
        $x = $this->transCandidates; if ($x != null) return $x;

        # Get translations of the parent
        $p = $this->getGlobalParent();
        if ($p == null) return null;
        $l = $p->getTranslations();

        $type = $this->getType();
        $c = [];
        $fl = $this->getLang();
        # Get children of the translations of the parent
        foreach ($l as $x) {
            # if 
            foreach ($x->children($type) as $y) {
                $c[] = $y;
            }
        }

        return $this->transCandidates = $c;

        # Get siblings
        $p = $this->getParent();
        if ($p == null) {
            $p = $this->getLanguageRoots();
        } else {
            $p = $p->children($this->getType());
        }

        
        $l = [];
        $fc = $this->getLang();
        foreach ($p as $x) {
            if ($fc && ($fc != $x->getLang())) continue;
            $t = $x->getTranslations();
            foreach ($t as $y) {
                $i = $y->item->getId();
                $l[$i] = $y;
            }
        }

        return $l;
    }
    private $listTransCandidates;
    public function listTransCandidates() {
        $x = $this->listTransCandidates; if ($x != null) return $x;
        $l = $this->getTransCandidates();
        # foreach ($l as $y) { if (!($y instanceof Page)) throw new \Exception("aha" . get_class($y)); }
        $n = [];
        foreach ($l as $x) {
            $i = $x->getIdName();
            $c = $x->getLang();
            $n[$i] = _("[" . $c . "]") . " " . $x->shortenedTitle(20);
        }
        return $this->listTransCandidates = $n;
    }
    public function getTransCandidate() { }

    public function shortenedTitle($n) {
        $t = $this->getTitle();
        if ($t == null) {
            return "";
        } else {
            if (strlen($t) > $n) {
                return substr($t, 0, $n - 1)."…";
            } else {
                return $t;
            }
        }
    }

    private $listTranslations;
    public function listTranslations() {
        $x = $this->listTranslations; if ($x != null) return $x;
        $l = $this->getTranslations(true);
        # foreach ($l as $y) { if (!($y instanceof Page)) throw new \Exception("aha" . get_class($y)); }
        $n = [];
        foreach ($l as $x) {
            $i = $x->getUrl();
            $c = $x->getLang();
            $n[$i.""] = _("[" . $c . "]") . " " . $x->shortenedTitle(20);
        }
        return $this->listTranslations = $n;
    }
    private $listOrCreateTranslations;
    public function listOrCreateTranslations() {
        $x = $this->listOrCreateTranslations; if ($x != null) return $x;
        $l = $this->getTranslations(true);
        $a = $this->getAvailableSiteLanguages();
        # foreach ($l as $y) { if (!($y instanceof Page)) throw new \Exception("aha" . get_class($y)); }
        $n = [];
        foreach ($l as $x) {
            $i = $x->getUrl();
            $c = $x->getLang();
            if (array_key_exists($c, $a)) unset($a[$c]);
            $n[$i.""] = _("[" . $c . "]") . " " . $x->shortenedTitle(20);
        }
        $u = $this->getUrl();
        foreach ($a as $x) {
            $i = $u->set("l", $x);
            $i = $i->set("createtype", $this->getType());
            if (!$this->isNew()) {
                $i = $i->set("createfrom", $this->getIdName());
            }
            $n[$i.""] = _("Traduci in") . " " . _($x . "_langname");
        }
        return $this->listOrCreateTranslations = $n;
    }
    public function getTranslation() { return $this->getUrl().""; }

    public function isTranslated() {
        $x = $this->getAvailableSiteLanguages;
        foreach ($this->getTranslations() as $t) {
            $l = $t->getLang();
            if (array_key_exists($l, $x)) unset($x[$l]);
        }
        return count($x) == 0;
    }
    
    # public function getChildrenType($x) { }
    public function listAllowedChildrenTypes() {
        $t = $this->allowed_children_types();
        if (count($t) > 0) {
            $r = [];
            foreach ($t as $x) $r[$x] = $x;
            return $r;
        }
        return [];
    }
    public function getPresentChildrenTypes() {
        # $l = $this->item->getChildren();
        # $t = [];
        # foreach ($l as $x) $t[] = $x->getType();
        # return $t;
        return $this->content->getChildrenTypes();
    }
    public function getAllowedChildrenTypes() {
        return getPresentChildrenTypes();
    }
    public function getChildrenTypes() {
        return getAllowedChildrenTypes();
    }
    public function getTypedChildren() {
        $t = getChildrenTypes();
        $r = [];
        foreach ($t as $t) {
            $r[$t] = $this->getChildren($t);
        }
        return $r;
    }
        
    # Adaptive form configuration
    public function canBeRenamed()     { return true; }
    public function hasVisibility()    { return true; }
    public function hasFixedLocation() { return false; }
    public function hasLargeText()     { return false; }

    public function buildForm($b) {
        if ($this->hasVisibility() && $this->isVisible()) $b->add('visible',  'checkbox');
        $b->add('title',    'text');
        if ($this->canBeRenamed()) $b->add('name', 'text');
        $c = $this->compatibleTypes();
        if (count($c) > 1) $b->add('type', 	'choice', [ 'choices' => $c ]);
        # if (!$this->hasFixedLocation()) {
        #     $c = $this->getSiblingTypes();
        #     $b->add('siblingtype', 	'choice', [ 'choices' => $c ]);
        # }
        $c = $this->listOrCreateTranslations();      if (count($c)) $b->add('translation', 	    'choice', [ 'choices' => $c, 'required' => false, 'attr' => [ 'class' => 'visiturl_val' ] ]);
        $c = $this->listTransCandidates();   if (count($c)) $b->add('transcandidate', 	'choice', [ 'choices' => $c, 'required' => false ]);
        $c = $this->listVersions();  if (count($c)) $b->add('version', 	'choice', [ 'choices' => $c, 'required' => false, 'attr' => [ 'class' => 'visiturl_val' ] ]);

        # foreach ($this->getPresentChilrenTypes() as $t) { }
        # $c = $this->listAllowedChildrenTypes();   if (count($c)) $b->add('childrentypes', 	'choice', [ 'choices' => $c, 'required' => false ]);
        # if (!$this->isVisible()) $b->add('publish',  'button');
        # $v = $this->getVersions();
        # $b->add('versions'

        foreach ($this->detail_names() as $d) {
            $x = $this->buildDetail($d);
            if ($x != null) $x->buildForm($b);
        }
        
        return $b;
    }

    public function detail_names() {
        $l = $this->item->getDetails();
        $n = [];
        foreach ($l as $x) $n[] = $x->getName();
        return $n;
    }

    protected $builtform;
    public function getBuiltForm() {
        $f = $this->builtform; if ($f != null) return $f;
        $b = $this->createFormBuilder($this);
        return $this->builtform = $this->buildForm($b)->getForm();
    }
    
    private $form;
    public function getForm() {
        $f = $this->form; if ($f != null) return $f;
        return $this->form = $this->getBuiltForm()->createView();
    }

    public $postmessage;
    public function addMessage($msg) {
        $m = $this->postmessage;
        if (empty($m)) $m = "";
        $this->postmessage = "<div>$msg</div>$m";
    }
    public function addErrorMessage($x) {    $this->addMessage($x); }
    public function addWarningMessage($x) {  $this->addMessage($x); }
    public static function format_query_data_as_html($x) {
        $r = "<dl>";
        foreach ($x as $a => $b) {
            $a = htmlspecialchars($a);
            $b = htmlspecialchars($b);
            $r .= "<dt>$a</dt><dd>$b</dd>";
        }
        $r .= "</dl>";
        return $r;
    }

    private $redir;
    protected function redir($request, $url) {
        if (isset($this->postmessage)) $request->getSession()->setFlash("message", $this->postmessage);
        $this->redir = $url;
    }

    protected function theLink() {
        $t = $this->getTitle();
        if (empty($t)) $t = "-";
        return "<a href=\"" . $this->getUrl() . "\"class=pagelink>" . $this->getTitle() . "</a>";
    }

    protected function theLang() {
        $l = $this->getLang();
        if (empty($l)) return "(Lingua non definita)";
        return _("[$l]");
    }

    protected function overlayVersion($verid) {
        if (($v = $this->content->getVersion($verid)) != null) {
            $this->versionofid = $this->getId();
            $this->item = $v->item;
        } else {
            $this->addErrorMessage(_("Non è stata trovata la versione richiesta: " . $verid));
        }
    }
    
    public function setupObject($request) {
        $theme = $this->page_theme = $request->get("theme");
        $versionid = $request->get("verid");
        # $this->postmessage = "Hi";
        if ($request->isMethod('POST')) {
            $formaction = $request->request->get("formaction");
            if ($formaction != null) $formaction = array_keys($formaction)[0];
            $f = $request->request->get("form");
            # transcandidate();
            $this->addMessage("Action: $formaction<div style='font-size:40%'>Data keys posted: " . self::format_query_data_as_html($f) . "</div>");
            try {
                set_error_handler(function($e, $s, $f = null, $l = null, $c = null) {
                    // throw new Exception("Error");
                    throw new \Exception("Error $e: $s" . (isset($f) ? " $f" : "") . (isset($l) ? ":$l" : "")); # . (isset($c) ? " ($c)" : ""));
                }, E_ALL);
                if ($formaction == "choosetrans") {
                    $x = $this->byidname($f["transcandidate"]);
                    $this->linkTranslation($x);
                    $this->addMessage("Traduzione \"" . $x->theLink() . "\" " . $x->theLang() . " collegata.");
                }
            } catch(\Exception $e) {
                $this->addMessage($e->getMessage());
            }
            $this->redir($request, $this->getUrl());
            $this->getBuiltForm()->bind($request);
        } else {
            $m = $request->getSession()->getFlash("message");
            if ($m != null) $this->addMessage($m);
        }
        if ($versionid != null) { $this->overlayVersion($versionid); }

        # if ($theme == "bb5")
            $this->template = "UnislugN2Bundle:bb5:index.html.twig";
        if ($theme == "base")
            $this->template = "UnislugN2Bundle::edit-page.html.twig";
    }

    public static function editContent($doc, $req, $controller, $prefix = null) {
        if ($prefix == null) {
            $c = new self($doc);
        } else {
            $c = $prefix . $doc->item->getType();
            $c = new $c($doc);
        }
        $c->setupObject($req);
        if (isset($c->redir)) return $controller->redirect($c->redir."");
        $c->getBuiltForm(); # Trigger all script inclusions
        return $c->render($controller);
    }

    public function spawnPage() {
        $x = $this->buildView($this->content->spawn());
        return $x;
    }

    public static function addContent($doc, $req, $controller, $prefix = null) {
        if ($prefix == null) {
            $c = new self($doc);
        } else {
            $c = $prefix . $doc->item->getType();
            $c = new $c($doc);
        }
        $newdoc = $c->spawnPage();
        return self::editContent($newdoc, $req, $controller, $prefix);
    }

    ####################################################

    private $versions;
    protected $versionof;
    public function getVersions() {
        $c = $this->versions; if ($c != null) return $c;
        $c = $this->item->getVersions();
        $r = [ ];
        foreach ($c as $i) $r[] = $this->buildVersionView($i);
        return $this->versions = $r;
    }
    public function buildVersionView($i) {
        $x = $i->view; if ($x != null) return $x;
        $x = $this->beget($i);        
        $i->route = $this->content;
        $i->view = $x;
        $x->content   = $this->content;
        $x->versionof = $this;
        $x->item = $i;
        return $x;
    }

    public function byidname($x) {
        $y = $this->content->byidname($x);
        if ($y == null) return null;
        return $this->buildView($y->item);
    }

    ####################################################
    # Facilities for registering libraries and resources
    private $registered_scripts;
    public function registerScript($name, $source) {
        $scripts = $this->registered_scripts;
        if ($scripts == null) $scripts = [];
        if (!isset($scripts[$name])) $scripts[$name] = $source;
        $this->registered_scripts = $scripts;
    }
    public function getRegisteredScripts() { $x = $this->registered_scripts; if ($x == null) return []; return array_values($x); }

    private $registered_css;
    public function registerCss($name, $source) {
        $css = $this->registered_css;
        if ($css == null) $css = [];
        if (!isset($css[$name])) $css[$name] = $source;
        $this->registered_css = $css;
    }
    public function getRegisteredCss() { $x = $this->registered_css; if ($x == null) return []; return array_values($x); }

    private $registered_snippets;
    public function registerSnippet($name, $snippet) {
        $snippets = $this->registered_snippets;
        if ($snippets == null) $snippets = [];
        if (!isset($snippets[$name])) $snippets[$name] = $snippet;
        $this->registered_snippets = $snippets;
    }
    public function getRegisteredSnippets() { $x = $this->registered_snippets; if ($x == null) return []; return array_values($x); }

    public function needsTinymce() {
        $this->labjs()->ask("tinymce");
    }
    private $labjs;
    public function labjs() {
        $x = $this->labjs;
        if ($x == null) {
            $x = new LabjsRequire;
            return $this->labjs = $x
                ->source("jquery",      "http://code.jquery.com/jquery-1.9.1.js" 						 )
                ->source("jqueryuri",   $this->respath("libraries/jquery.uri-1.1.2.min.js"),  		     "jquery")
                ->source("jqueryui",    "http://code.jquery.com/ui/1.10.1/jquery-ui.js",   				 "jquery")
                ->source("jqueryform",  $this->respath("libraries/jquery.form.js"),  	   				 "jquery")
                ->source("tinymce",     $this->respath("tinymce/jscripts/tiny_mce/jquery.tinymce.js"),   "jquery");
        }
        return $x;
    }

    public function buildDetail($n) { return null; }

    public function linkTranslationOk($x, $check_inverse = true) {
        if ($this->getType() != $x->getType()) {
            throw new \Exception(_("La pagina tradotta selezionata (" . $x->getTitle() . ") è di differente tipo.  Convertire il tipo prima di collegare la traduzione."));
        }
        if ($check_inverse) $x->linkTranslationOk($this, false);
    }

    public function linkTranslation($x) {
        if ($x == null) throw new \Exception(_("Traduzione da collegare non trovata"));

        $this->linkTranslationOk($x);

        $this->content->linkTranslation($x->item);
        
        # $x->linkTranslationOk($this);

        

        # if ($x == ) throw new Exception(_("La pagina è già collegata ad un altra traduzione"))
        
        # Conditions:
        # Parents are null or distinct
        # Parents are null or have the same language as the child, with the exception of language roots
        # More importantly: parents are null or translations of each other
    }

    public function unlinkTranslation() {
        # TODO
    }

    private $details;
    public function detail($k, $p = null) {
        // $this->addMessage($this->item->getId() . ": Get $k");
        if (isset($this->details)) {
            $m = $this->details;
            if (array_key_exists($k, $m)) {
                return $m[$k];
            } else {
                return parent::detail($k, $p);
                # return $p;
            }
        } else {
            return parent::detail($k, $p);
        }
    }
    public function setDetail($k, $v) {
        // $this->addMessage($this->item->getId() . ": Set $k");
        if (!isset($this->details)) $this->details = [];
        $this->details[$k] = $v;
    }
    
    public function getSiteContents() {
        return [];
    }

    public function getSiteTitle() { return null; }

    # private $contentLanguageControl;
    # public function getContentLanguageControl() {
    #     $x = $this->contentLanguageControl; if ($x != null) return $x;
    #     $translations = $this->listTranslations();
    #     $transCandidates = $this->listTransCandidates();
    #     $x = "<span class=\"navbar-text\">Hello</span>";
    #     return $this->contentLanguageControl = $x;
    # }

    # private $versionsControl;
    # public function getVersionsControl() {
    #     $translations = $this->listVersions();        
    # }

    private $isNew;
    public function isNew() {
        return $this->content->isNew();
    }
    
    public function hideChildrenPane() {
        return $this->isNew();
    }

    public function canShowBreadcrumb() {
        return true;
    }
}
