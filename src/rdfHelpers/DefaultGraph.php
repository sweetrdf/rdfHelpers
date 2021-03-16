<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace rdfHelpers;

use BadMethodCallException;
use Stringable;
use rdfInterface\Term;
use rdfInterface\DefaultGraph;

/**
 * Description of DefaultGraph
 *
 * @author zozlak
 */
class DefaultGraph implements DefaultGraph {

    public function __construct() {
        
    }

    public function __toString(): string {
        return $this->getType();
    }

    public function getType(): string {
        return rdfInterface\TYPE_DEFAULT_GRAPH;
    }

    public function getValue(): int | float | string | bool | Stringable {
        throw new BadMethodCallException();
    }

    public function equals(Term $term): bool {
        return $term->getType() === $this->getType();
    }
}