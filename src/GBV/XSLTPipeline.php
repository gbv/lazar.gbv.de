<?php declare(strict_types=1);

namespace GBV;

use DOMDocument;
use DOMNode;
use XSLTProcessor;

class XSLTPipeline
{
    protected $steps = [];

    public function appendFiles(array $filenames)
    {
        foreach ($filenames as $file) {
            $doc = new DOMDocument();
            $doc->load($file);

            $xsl = new XSLTProcessor();
            $xsl->importStyleSheet($doc);

            $this->steps[] = $xsl;
        }
    }
    
    public function transformToDoc(DOMNode $node): DOMDocument
    {
        if (count($this->steps)) {
            foreach ($this->steps as $step) {
                $node = $step->transformToDoc($node);
                if (!$node || !$node->documentElement) {
                    return new DOMDocument();
                }
            }
            return $node;
        } else {
            if ($node instanceof DOMDocument) {
                return $node;
            } else {
                $doc = new DOMDocument();
                $doc->appendChild($doc->importNode($node, true));
                return $doc;
            }
        }
    }
}
