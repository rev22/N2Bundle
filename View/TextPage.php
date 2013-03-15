<?php

namespace Unislug\N2Bundle\Page;

class TextPage extends Page
{
    public function modelName() { return "text-page"; }

    public function getText() {
        $this->detail("Text", "");
    }

}
