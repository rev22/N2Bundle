<?php

namespace Unislug\N2Bundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class N2itemToIdnameTransformer implements DataTransformerInterface
{
    private $content;

    public function __construct($content) {
        $this->content = $content;
    }

    /**
     * Transforms a N2 item to an id:name combination.
     *
     * @param  N2item|null $issue
     * @return string
     */
    public function transform($item) {
        if (null === $item) return "";
        return $item->getId() . ':' . $item->getName();
    }

    public function reverseTransform($idname) {
        if ($idname == null || $idname == "") return null;
        $v = $content->getbyidname($idname);
        if (null == $v) throw new TransformationFailedException("N2 Item $idname does not exist!");
        return $v;
    }
}
