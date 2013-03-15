<?php

namespace Unislug\N2Bundle\Controller;

# use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
# use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Unislug\N2Bundle\Content\Content;
use Unislug\N2Bundle\View\Page;
use Unislug\N2Bundle\Edit\EditPage;
use Unislug\N2Bundle\Edit\Globals;
use Unislug\N2Bundle\Router\SlugRoute;
use Unislug\N2Bundle\Form\UserLoginFormType;
use Unislug\N2Bundle\Misc\Uri;

use Symfony\Component\HttpFoundation\Response;

use Exception;

class ExceptionalResponse extends Exception {
    protected $response;
    public function __construct(Response $response) {
        $this->response = $response;
        parent::__construct("Uncaught exceptional HTTP Response");
    }
    public function getResponse() {
        return $this->response;
    }
}

class PathController extends Controller
{
    public function setupgettext($locale) {
        $kernel = $this->get('kernel');
        $locale = $kernel->mapLocaleForGettext($locale);

        if ($locale != null) {
            putenv('LC_MESSAGES='.$locale);
            setlocale(LC_MESSAGES, $locale);
            // putenv("LANG=" . $locale);
            // setlocale(LC_MESSAGES, "");
        
            // Specify location of translation tables
            bindtextdomain("UnislugN2Bundle", $kernel->getRootDir() . "/locale/");
        
            // Choose domain
            textdomain("UnislugN2Bundle");

            // throw new \Exception(gettext("it.name") . "::" . $locale);
        }
        
        // Translation is looking for in ./locale/de_DE/LC_MESSAGES/myPHPApp.mo now
        
        // Print a test message
        // echo gettext("Welcome to My PHP Application");
        
        // Or use the alias _() for gettext()
        // echo _("Have a nice day");
    }
    public function indexAction(Request $request, $_route, $path = "")
    {
        $respath = $request->getBasePath();
        $baseurl = Uri::fromParts($request->getScheme(), $request->getHttpHost(), null, $respath);
        $requri = Uri::gen($request->getUri());
        $container = $this->container;
        $slugroute = SlugRoute::parse($path, $_route, $container, $respath, $baseurl);

        # Normalize urls for content
        if ($path != $slugroute->path()) {
            $q = $request->getQueryString();
            if (($q == null) || ($q == "")) { $q = ""; } else { $q = "?$q"; }
            return $this->redirect($slugroute->uri() . $q, 301);
            # $redir = new RedirectController;
            # $redir->setContainer($this->container);
            # return $redir->urlRedirectAction(rtrim($this->generateUrl($_route), "/") . "/" . $slugroute->path, false);
        }

        # Setup locale
        $sitedefaultlocale  = $container->getParameter("locale");
        $contentlocale  	= $slugroute->base()->locale; if (empty($contentlocale)) $contentlocale = $sitedefaultlocale;
        # $locale = $request->get("_locale");
        $locale = $this->get('translator')->getLocale();
        # $query->get("hl");
        $this->setupgettext($locale);

        $query = $request->query;
        $action = $query->get("action");
        $switch_to_locale = $query->get("l");
        $manager = $this->getDoctrine()->getManager();
        
        try {
            $httphost = $request->getHttpHost();
            if ($action) {
                if ($switch_to_locale != null) {
                    return $this->switchLocale($slugroute, $manager, $switch_to_locale, $requri);
                }
                # Convert any json data
                if ($request->headers->get('Content-Type') == 'application/json') {
                    $data = json_decode($request->getContent(), true);
                    $request->request->replace(is_array($data) ? $data : array());
                }
                if ($action == 'login') {
                    $this->requireAuthentication($request, $slugroute, $manager);
                    return $this->loginAction($request, $slugroute);
                } elseif ($action == 'logout') {
                    return $this->logoutAction($request, $slugroute);
                } elseif ($action == 'rawedit') {
                    $this->requireAuthentication($request, $slugroute, $manager);
                    $doc = Content::retrieve($manager, $slugroute);
                    if ($doc == null) return $this->handleNoContent();
                    return EditPage::editContent($doc, $request, $this);
                } elseif ($action == 'edit') {
                    $this->requireAuthentication($request, $slugroute, $manager);
                    $leftover = [];
                    $doc = Content::retrieve_deepest($manager, $slugroute, null, $leftover);
                    if ($doc == null) return $this->handleNoContent();
                    if ($leftover) {
                        if (count($leftover) == 1) {
                            return $this->addContent($doc, $request);
                        } else {
                            return $this->handleNoContent();
                        }
                    } else {
                        return $this->editContent($doc, $request);
                    }
                } elseif ($action == 'add') {
                    $this->requireAuthentication($request, $slugroute, $manager);
                    $doc = Content::retrieve($manager, $slugroute);
                    if ($doc == null) return $this->handleNoContent();
                    return $this->addContent($doc, $request);
                } elseif (preg_match("/edit-([a-z]+)/", $action, $matches)) {
                    $this->requireAuthentication($request, $slugroute, $manager);
                    return Globals::authorGlobal($matches[1], $manager, $slugroute, $request, $this);
                } elseif (preg_match("/data:(.*)/", $action, $data)) {
                    # Perform a data command
                    $data = explode(":", $data[1]);
                    return $this->data($request, $slugroute, $manager, $data);
                }
            }
            if (($switch_to_locale != null) && ($switch_to_locale != $contentlocale)) {
                return $this->switchLocale($slugroute, $manager, $switch_to_locale);
            }
            if ($contentlocale != null) {
                # $request->setLocale($contentlocale);
                $translator = $this->get('translator');
                if ($translator != null) {
                    $translator->setLocale($contentlocale);
                }
            }
            return $this->serveContent($slugroute, $manager, $query);
        } catch (ExceptionalResponse $a) { return $a->getResponse(); }
    }

    protected function handleNoContent($message = null) {
        return new Response(
            $this->render('UnislugN2Bundle::layout.html.twig', [
                              'title' => "Resource was not found",
                              'body' => ($message != null) ? $message : "Could not retrieve the requested resource.",
                              ]), 404);
    }

    protected function switchLocale($slugroute, $manager, $l, $requri = null) {
        $c = Content::retrieve_deepest($manager, $slugroute, null, $leftover);
        if ($c == null) return $this->handleNoContent();
        if ($leftover == null) $leftover = [];
        return $this->switchLocaleFromContent($c, $l, $requri, $leftover);
    }
    protected function serveContent($slugroute, $manager, $query) { 
        $doc = Content::retrieve($manager, $slugroute);
        if ($doc == null) return $this->handleNoContent();
        return $this->renderContent($doc);
    }

    protected function renderContent($doc)   		{ return Page::renderContent($doc, $this); }
    protected function switchLocaleFromContent($c, $l, $requri = null, $leftover = [])  {
        $p = new Page($c);
        if ($p == null) return $this->handleNoContent();
        $leftover2 = [];
        $t = $p->getNearestTranslation($l, $leftover2);
        foreach ($leftover2 as &$x) $x = $x->item->getName();
        $leftover = array_merge($leftover2, $leftover);
        if ($t == null) return $this->handleNoContent();
        if ($requri == null) {
            return $this->redirect($t->getUrl());
        } else {
            return $this->redirect(Uri::gen($t->getUrl())->sub(implode("/", $leftover))->merge($requri->del("l")->getQuery()) . "");
        }
    }
    protected function addContent($d, $r)   		{ return EditPage::addContent($d, $r, $this); }
    protected function editContent($d, $r)   		{ return EditPage::editContent($d, $r, $this); }


    # User-related controllers
    public function loginAction($request, $slugroute, $manager = null, $returnpath = null)
    {
        $form = $this->createForm(new UserLoginFormType($request));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                $d = $form->getData();
                $username    = $d['username'];
                                                  
                $password    = $d['password'];
                $returnpath  = $d['_returnpath'];

                $s = $request->getSession('session');
                # time_nanosleep($sec, $usec);
                # $s->set('username', $username);
                # $salt = md5("$username/$sec/$usec/$password");
                # $s->set('salt', $salt);
                # $hmac = md5("$salt/$username/$password");
                # $s->set('hmac', $hmac);

                $s = $request->getSession();
                $s->set("username", $username);
                $s->set("password", $password);
                # $s->set("authdata", "$salt/$hmac");

                if ($returnpath == null) {
                    return $this->redirect($slugroute->uri());
                } else {
                    return $this->redirect($returnpath);
                }
            }
        }
        if ($returnpath != null) $form->setData([ '_returnpath' => $returnpath ]);
        return new Response($this->renderView('UnislugN2Bundle:User:login.html.twig', array('form'  => $form->createView())));
    }
    
    public function logoutAction($request, $slugroute)
    {
        $session = $request->getSession();
        # $session->set("username", null);
        $session->invalidate();
        return $this->redirect($slugroute->base()->uri());
    }

    public function requireAuthentication($request, $slugroute, $manager = null) {
        $session = $request->getSession();
        $username = $session->get("username");
        $password = $session->get("password");
        $returnpath = $request->getUri();
        # $q = $request->getQueryString();
        # if ($q) $returnpath = $returnpath . $q;
        if ($password != "2013") throw new ExceptionalResponse($this->loginAction($request, $slugroute, $manager, $returnpath));
    }

    public function data($request, $slugroute, $manager, $command) {
        $verb = $command[0];
        try {
            set_error_handler(function($e, $s, $f = null, $l = null, $c = null) {
                // throw new Exception("Error");
                throw new Exception("Error $e: $s" . (isset($f) ? " $f" : "") . (isset($l) ? ":$l" : "")); # . (isset($c) ? " ($c)" : ""));
            }, E_ALL);
            if ($verb == 'echo') {
                return new Response(json_encode($request->getContent()), 202);
            } elseif ($verb == 'connect-translation') {
                $r = $request->request;
                $a = $r->get('a');
                $b = $r->get('b');
                Content::connect_translations($a, $b);
                return new Response("Translations connected", 202);
            } elseif ($verb == 'reorder-items') {
                $seq = $request->request->get('seq');
                $seq = explode(".", $seq);
                Content::reorder_test($manager, $seq);
                return new Response("Items reordered", 202);
            }
            return new Response("Unknown verb $verb", 501);
        } catch (Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }
}
