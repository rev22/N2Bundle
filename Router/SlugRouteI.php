<?php

namespace Unislug\N2Bundle\Router;

interface SlugRouteI {
    public function base();
    public function slugs();
    public function path($slug = null);  # similar to uri but returns the {path} component for the symfony route
    public function uri($slug = null);
    public function sub($slug);
}
