<?php

namespace Unislug\N2Bundle\Content;

use Unislug\N2Bundle\Entity\N2item;

class Content {
    public $item;
    protected $manager;
    protected $route;

    # Only subnodes
    protected $base; # Should link the global content root, null if this is a global content root
    protected $parent;

    public function __construct( $item, $manager, $route ) {
        $this->manager  = $manager;
        $this->route    = $route;
        if ($item != null) {
            $this->item 	= $item;
            $item->route    = $this;
        }
    }

    public function asItem() { return $this->item; }
    public function getId() { return $this->item->getId(); }
    public function getParents() {
        $p = [];
        $i = $this;
        while (isset($i->parent)) $p[] = $i = $i->parent;
        return array_reverse($p);
    }
    public function getCmsParents() {
        $p = [];
        $i = $this;
        while (isset($i->parent) && isset($i->parent->route)) {
            $p[] = $i = $i->parent;
            if ($i->route->isRoot()) break;
        }
        return array_reverse($p);
    }
    public function getParent() {
        if (isset($this->parent)) return $this->parent->item;
        return null;
    }
    public function getRoot() { return $this->base()->item; }
    public function getChildren( $type = null, $queryappend = null) {
        if ($this->isNew()) return [];
        if ($type == null) {
            if ($queryappend == null) $queryappend = " AND i.visible = 1 AND i.name != 'Header' ORDER BY i.sortorder";
            $l = $this->manager->createQuery(
                "SELECT i FROM UnislugN2Bundle:N2item i WHERE i.parentid = " . $this->item->getId() . $queryappend)
                ->getResult();
            foreach ($l as $x) $this->sub($x);
            return $l;
        } else {
            if ($queryappend == null) $queryappend = " ORDER BY i.sortorder";
            $l = $this->manager->createQuery(
                "SELECT i FROM UnislugN2Bundle:N2item i WHERE i.parentid = " . $this->item->getId() . " AND i.type = '$type'" . $queryappend)
                ->getResult();
            foreach ($l as $x) $this->sub($x);
            return $l;
        }
    }
    public function getChildrenByTypeAndTitle($type, $title) {
        if ($this->isNew()) return [];
        $l = $this->manager->createQuery(
            "SELECT i FROM UnislugN2Bundle:N2item i WHERE i.parentid = :id AND i.type = :type AND i.title = :title")
            ->setParameters([ "id" => $this->getId(), "type" => $type, "title" => $title ])
            ->getResult();
        foreach ($l as $x) $this->sub($x);
        return $l;
    }
    public function getChildrenByTypeAndDetailStringValue($type, $detail, $value) {
        if ($this->isNew()) return [];
        $l = $this->manager->createQuery(
            "SELECT i FROM UnislugN2Bundle:N2item i JOIN i.details d WHERE i.parentid = :id AND i.type = :type AND d.name = :detail AND d.stringvalue = :value")
            ->setParameters([ "id" => $this->getId(), "type" => $type, "detail" => $detail, "value" => $value ])
            ->getResult();
        foreach ($l as $x) $this->sub($x);
        return $l;
    }
    public function getChildrenTypes($queryappend = "") {
        if ($this->isNew()) return [];
        $l = $this->manager->createQuery(
            "SELECT DISTINCT i.type FROM UnislugN2Bundle:N2item i WHERE i.parentid = " . $this->item->getId() . $queryappend)
            ->getResult();
        foreach ($l as &$x) $x = $x['type'];
        return $l;
    }
    public function getTranslations() {
        if ($this->isNew()) return [];
        $t = $this->item->getTranslationofid();
        if ($t == null) {
            $ti = $this->item->getId();
        } else {
            $ti = $t->getId();
        }
        $l = $this->manager->createQuery(
            "SELECT i FROM UnislugN2Bundle:N2item i WHERE i.translationofid = $ti AND i.versionofid IS NULL")
            ->getResult();
        foreach ($l as &$x) $x = $this->buildRoute($x);
        $l[] = ($t != null) ? $this->buildRoute($t) : $this;
        return self::index_by_id($l);
    }
    public function getTranslation($l, $ancestors = false, &$leftover = []) {
        $tl = $this->item->getLang();
        if (($tl != null) && ($tl == $l)) return $this->item;
        $to = $this->item->getTranslationofid();
        if ($to == null) {
            $ti = $this->item->getId();
        } else {
            $tol = $to->getLang();
            if (($tol != null) && ($tol == $l)) return $to;
            $ti = $to->getId();
        }
        $items = $this->manager->getRepository("UnislugN2Bundle:N2item")
            ->findBy([ "translationofid" => $ti, "versionofid" => null, "lang" => $l ]);
        if (empty($items)) {
            if ($ancestors) {
                $p = $this->item->getParentid();
                if ($p == null) return null;
                if (isset($p->route)) {
                    array_unshift($leftover, $this);
                    return $p->route->getTranslation($l, true, $leftover);
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else if (count($items) > 1) {
            throw new \Exception("Multiple translations with the same language were found");
        } else {
            return $items[0];
        }
    }
    # public function getTranslationsOf() {
    #     $id = $this->item->getId();
    #     $l = $this->manager->createQuery(
    #         "SELECT i FROM UnislugN2Bundle:N2item i WHERE i.translationofid = $id")
    #         ->getResult();
    #     return $l;
    # }
    public function getSiblings( $type = null ) { return $this->parent->getChildren(); }
    #     $x = $this->item->getParentid();
    #     if ($x == null) return [];
    #     $x = $x->getId();
    #     $l = $this->manager->createQuery(
    #         "SELECT i FROM UnislugN2Bundle:N2item i WHERE i.parentid = $x AND i.visible = TRUE ORDER BY i.sortorder")
    #         ->getResult();
    #     foreach ($l as $x) $x->route = $this->sub($x);
    #     return $l;
    # }
    # public function getVersions() {
    #     return $this->manager->createQuery(
    #         "SELECT i FROM UnislugN2Bundle:N2item i WHERE i.versionofid = " . $this->item->getId())
    #         ->getResult();
    # }
    public static function rootQueryString() {
        return "SELECT j.id FROM UnislugN2Bundle:N2item j WHERE j.type = 'StartPage' AND j.parentid IN (SELECT k.id FROM UnislugN2Bundle:N2item k WHERE k.parentid IS NULL and k.versionofid IS NULL)";
    }
    public static function localeQueryString($locale) {
        return "SELECT l.id FROM UnislugN2Bundle:N2item l WHERE l.name = '$locale' AND l.parentid IN (" . self::rootQueryString() . ")";
    }
    public static function retrieve_deepest($m, $s, $o = null, &$leftover = null) {
        $x = self::retrieve($m, $s, $o, $deepest, $leftover); if ($x != null) return $x;
        if ($deepest == null) return null;
        $b = $s->base()->defaultRoot();
        return (new self(null, $m, $b))->buildRoute($deepest);
    }
    public static function retrieve($manager, $slugroute, $opts = null, &$deepest = null, &$leftover = null) {
        $items = [];
        $locale = $slugroute->base()->locale;
        $slugs = $slugroute->slugs();
        $nslugs = count($slugs);
        $join = "";
        # $join = " JOIN i.allowedroles r JOIN i.details d JOIN i.detailcollections c";
        # $join = " JOIN i.details d";
        # $join = "";
        # $extra = " AND i.visible = 1";
        $extra = "";
        if ($locale == null) {
            $root = "IN (" . self::rootQueryString() . ")"; # $root = "2"; # $manager->createQuery()->setMaxResults(1)->getOneOrNullResult();
        } else {
            $root = "IN (" . self::localeQueryString($locale) . ")";
        }
        # if ($opts) {
        #     if (isset($opts["type"])) {
        #         $t = $opts["type"];
        #         $root = "IN (SELECT j.id FROM UnislugN2Bundle:N2item j WHERE j.type = '$t' AND j.parentid IN (SELECT k.id FROM UnislugN2Bundle:N2item k WHERE k.parentid IS NULL and k.versionofid IS NULL))";
        #     }
        # }
        # $root = "= 2";
        if (empty($slugs)) {
            $item = $manager
                ->createQuery(
                    # "SELECT i FROM UnislugN2Bundle:N2item i WHERE i.parentid IS NULL AND i.versionofid IS NULL"
                    "SELECT i FROM UnislugN2Bundle:N2item i$join WHERE i.id $root$extra"
                    )
                ->setMaxResults(1)
                ->getOneOrNullResult();
            if ($item == null) {
                $leftover = $slugs;
                return null;
            }
            $items[] = $deepest = $item;
        } elseif (count($slugs) > 1) {
            $parent = null;
            $dir = $slugs[0];
            $item = $manager
                ->createQuery(
                    "SELECT i FROM UnislugN2Bundle:N2item i$join WHERE i.name = :slug AND i.parentid $root$extra")
                ->setParameter("slug", $dir)
                ->setMaxResults(1)
                ->getOneOrNullResult();
            if ($item == null) {
                $leftover = $slugs;
                return null;
            }
            array_shift($slugs); $parent = $deepest = $item; $parentid = $parent->getId();
            while (isset($item) && count($slugs) > 0) {
                $items[] = $parent;
                $dir = $slugs[0];
                $item = $manager->createQuery(
                    "SELECT i FROM UnislugN2Bundle:N2item i$join WHERE i.parentid = '$parentid' AND i.name = :slug$extra")
                    ->setParameter("slug", $dir)
                    ->setMaxResults(1)
                    ->getOneOrNullResult();
                if ($item == null) {
                    $leftover = $slugs;
                    return null;
                }
                array_shift($slugs); $parent = $deepest = $item; $parentid = $parent->getId();
                $last = $dir;
            }
            if ($item == null) {
                $leftover = $slugs;
                return null;
            }
            $items[] = $item;
        } else {
            $slug = $slugs[0];
            $item = $manager
                ->createQuery(
                    "SELECT i FROM UnislugN2Bundle:N2item i$join WHERE i.name = :slug AND i.parentid $root$extra")
                ->setParameter("slug", $slug)
                ->setMaxResults(1)
                ->getOneOrNullResult();
            if ($item == null) {
                $deepest = $manager
                    ->createQuery(
                        "SELECT i FROM UnislugN2Bundle:N2item i$join WHERE i.id $root$extra")
                    ->setMaxResults(1)
                    ->getOneOrNullResult();
                $leftover = $slugs;
                return null;
            }
            $items[] = $deepest = $item;
        }

        $b = $slugroute->base()->defaultRoot();
        $i = end($items);
        return (new self(null, $manager, $b))->buildRoute($i);

        # while (($n = $i->getParentId()) != null) {
        #     $r = $n;
        # }

        # new self(, $manager, )
        # $i = getParentId();
        # $i = null;
        # if ($nslugs > 0) {
        #     $i = $items[0]->getParentid();
        # } else {
        #     $i = array_shift($items);
        # }

        # $x = new self($i, $manager, $slugroute->defaultRoot());
        # foreach ($items as $p) $x = $x->sub($p);
        # return $x;
    }

    public function sub($i) {
        $n = $i->getName();
        if ($i->getParentid() !== $this->item) {
            $ep = $this->item->getName();
            $fp = 'null';
            $ip = $i->getParentid();
            if ($ip != null) {
                $fp = $ip->getName();
            }
            throw new \Exception("Unlinked subcontent: $n, parent is $fp should have been $ep");
        }
        $x = new self($i,
                     $this->manager,
                     $this->route->sub($n));
        $x->base = $this->base();
        $x->parent = $this;
        return $x;
    }
    
    # SlugRoute compatible interface
    public function base() 			   { return $this->base == null ? $this : $this->base;  }
    public function uri($slug = null)  { return $this->route->uri($slug);                   }

    public function buildRoute($i) {
        $c = $i->route; if ($c != null) return $c;
        $t = $i->getType();
        if ($t != null) {
            if ($t == "StartPage") {
                return new self($i, $this->manager, $this->route->base()->defaultRoot());
            }
        }
        $p = $i->getParentid();
        if ($p == null) {
            throw new \Exception("Object was found outside of a root: " . $i->getId() . ":" . $i->getName());
        }
        if (($t != null) && ($t == "LanguageRoot")) {
            $x = $this->buildRoute($p)->sub($i);
            $n = $x->item->getName();
            if ($n != null && $n != "") {
                $x->route = $this->route->base()->languageRoot($n);
            }
            return $x;
        } else {
            return $this->buildRoute($p)->sub($i);
        }
    }

    # Only top node
    private $container;
    public function getContainer() {
        $x = $this->base()->container;
        if ($x != null) return $x;
        return $this->base()->container = $this->base()->route->base()->getContainer();
    }

    # Aux functions
    private $respath;
    public function respath($x = null) {
        $r = $this->respath;
        if ($r == null) $r = $this->respath = $this->route->base()->respath;
        if ($x == null) {
            return $r;
        } elseif (substr($x, 0, 1) == "/") {
            return $r . $x;
        } else {
            return $r . "/" . $x;
        }
    }
    private $cmspath;
    public function cmspath($x = null) {
        $r = $this->cmspath;
        if ($r == null) $r = $this->cmspath = rtrim($this->route->base()->defaultRoot()->uri(), "/");
        if ($x == null) {
            return $r;
        } elseif (substr($x, 0, 1) == "/") {
            return $r . $x;
        } else {
            return $r . "/" . $x;
        }
    }
    private $cmsfullurl;
    public function cmsfullurl($x = null) {
        $r = $this->cmsfullurl;
        if ($r == null) $r = $this->cmsfullurl = rtrim($this->route->base()->baseurl, "/");
        if ($x == null) {
            return $r;
        } elseif (substr($x, 0, 1) == "/") {
            return $r . $x;
        } else {
            return $r . "/" . $x;
        }
    }
    private $languageRoot;
    public function languageRoot() {
        $r = $this->languageRoot; if ($r != null) return $r;
        $i = $this->item;
        $t = $i->getType();
        if (($t == "StartPage") || ($t == "LanguageRoot")) {
            $r = $i;
        } else {
            $p = $i->getParentid();
            if ($p == null) {
                $r = $i;
            } else {
                $r = $p->route->languageRoot();
            }
        }
        return $this->languageRoot = $r;
    }

    # Authoring
    public static function reorder($repo, $seq) {
        
        $items = $repo
            ->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.id IN (" . implode(', ', $seq) . ") ORDER BY i.sortorder")
            //->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.itemid IN ?1 ORDER BY i.sortorder")
            //->setParameter(1, $seq)
            ->execute();

        $items_by_id = [];
        
        foreach ($items as $i)
            $items_by_id[$i->getId()] = $i;

        $y = [];
        
        for ($i = 0; $i < count($items); $i++)
            $y[$i] = [ $seq[$i], $items[$i]->getId() ];

        for ($i = 0; $i < count($items); $i++)
            $items_by_id[ $y[$i][1] ]->setSortorder($y[$i][0]);

        foreach ($items as $i) $repo->persist($i);
        
        $repo->flush();
    }

    public static function index_by_id($items) {
        $m = [];
        foreach ($items as $x) $m[$x->getId()] = $x;
        return $m;
    }
    
    public static function reorder_test($repo, $seq) {
        # throw new \Exception("No actual sequence found");
        
        $items = $repo
            ->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.id IN (" . implode(', ', $seq) . ") ORDER BY i.sortorder")
            //->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.itemid IN ?1 ORDER BY i.sortorder")
            //->setParameter(1, $seq)
            ->getResult();

        $items_by_id = self::index_by_id($items);

        $l = [];
        foreach ($seq as $x) {
            if (array_key_exists($x, $items_by_id)) {
                $l[] = $x; }}
        $seq = $l;

        if (empty($seq)) throw new \Exception("No actual sequence found");

        $i = 0;
        $p = null;
        foreach ($seq as $x) {
            # throw new \Exception("Here");
            $s = $items[$i]->getSortorder();
            # throw new \Exception("Here2");
            if ($p != null) {
                if ($p >= $s) {
                    $s = $p + 1;
                }
            }
            $p = $s;
            # throw new \Exception("Setting n2item$x's sortorder from " . $items_by_id[$x]->getSortOrder() . " to $s");
            $items_by_id[$x]->setSortorder($s);
            $i++;
        }

        # Now check the order
        $p = null;
        foreach ($seq as $x) {
            $s = $items_by_id[$x]->getSortOrder();
            if ($p != null) {
                if ($s <= $p) {
                    throw new \Exception("internal sorting function error");
                }
            }
            $p = $s;
        }
        
        foreach ($items as $i) $repo->persist($i);
        
        $repo->flush();
    }

    public function byidname($x) {
        $repo = $this->manager;
        list($id, $name) = explode(":", $x);
        $x = $repo
            ->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.id = :id AND i.name = :name ORDER BY i.sortorder")
            // ->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.itemid IN ?1 ORDER BY i.sortorder")
            ->setParameter("id", $id)
            ->setParameter("name", $name)
            ->getOneOrNullResult();
        if ($x == null) return null;
        return $this->buildRoute($x);
    }

    # Unlink a group of translations from any external or internal link
    public function unlinkTranslations($l, $flush = true) {
        $repo = $this->manager;
        $l = self::index_by_id($l);
        $x = $repo
            ->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.id IN (:ids) OR i.translationofid IN (:ids)")
            // ->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.itemid IN ?1 ORDER BY i.sortorder")
            ->setParameter("ids", array_keys($l))
            ->getResult();
        foreach ($x as $i) $i->setTranslationofid(null);
        foreach ($x as $i) $repo->persist($i);
        if ($flush) { $repo->flush(); }
    }

    public function linkTranslations($l, $verify_lang = true) {
        $l = self::index_by_id($l);
        $repo = $this->manager;
        $l[] = $this->item;
        $l = self::index_by_id($l);
        $this->unlinkTranslations($l, false);
        $i = $this->getId();
        unset($l[$i]);
        $this->item->setTranslationofid(null);
        $repo->persist($this->item);
        foreach ($l as $x) {
            $x->setTranslationofid($this->item);
            $repo->persist($x);
        }
        $repo->flush();
    }

    public function linkTranslation($x) {
        $repo = $this->manager;
        $l = $this->getTranslations();
        $l[] = $x;
        foreach ($l as &$x) $x = $x->asItem();
        $this->linkTranslations($l);
    }

    public function getVersion($id) {
        $repo = $this->manager;
        $x = $repo
            ->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.id = :id AND i.versionofid = :x")
            // ->createQuery("SELECT i FROM UnislugN2Bundle:N2item i WHERE i.itemid IN ?1 ORDER BY i.sortorder")
            ->setParameter("id", $id)
            ->setParameter("x", $this->getId())
            ->getOneOrNullResult();
        if ($x == null) return null;
        $content = clone $this;
        $content->item = $x;
        $x->route = $content;
        return $content;
    }

    public function getCmsRoot() {
    }

    public function getLanguageRoots() {
        $repo = $this->$manager;
        TODO();
    }

    # Generating entities
    private $new_entity;
    public function isNew() { return $this->new_entity; }

    public function spawn() {
        $i = new N2item;
        $i->setParentid($this->item);
        $i->setName("new-item");
        $i->setLang($this->item->getLang());
        $i = $this->sub($i);
        $i->new_entity = true;
        return $i->item;
    }
}
