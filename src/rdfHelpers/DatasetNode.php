<?php

/*
 * The MIT License
 *
 * Copyright 2023 zozlak.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace rdfHelpers;

use Generator;
use rdfInterface\DatasetNodeInterface;
use rdfInterface\DatasetInterface;
use rdfInterface\TermInterface;
use rdfInterface\QuadIteratorInterface;
use rdfInterface\QuadIteratorAggregateInterface;
use rdfInterface\QuadCompareInterface;

/**
 * Description of Node
 *
 * @author zozlak
 */
class DatasetNode implements DatasetNodeInterface {

    private DatasetInterface $dataset;
    private TermInterface $node;

    public function __construct(DatasetInterface $dataset, TermInterface $node) {
        $this->dataset = $dataset;
        $this->node    = $node;
    }

    public function getDataset(): DatasetInterface {
        return $this->dataset;
    }

    public function getNode(): TermInterface {
        return $this->node;
    }

    public function withDataset(DatasetInterface $dataset): DatasetNodeInterface {
        return new DatasetNode($dataset, $this->node);
    }

    public function withNode(TermInterface $term): DatasetNodeInterface {
        return new DatasetNode($this->dataset, $term);
    }

    /**
     * Returns QuadIteratorInterface iterating over node's quads.
     * 
     * If $filter is provided, the iterator includes only quads matching the
     * filter.
     * 
     * $filter can be specified as:
     * 
     * - An object implementing the \rdfInterface\QuadCompareInterface
     *   (e.g. a single Quad)
     * - An object implementing the \rdfInterface\QuadIteratorInterface
     *   (e.g. another Dataset)
     * - A callable with signature `fn(\rdfInterface\QuadInterface, \rdfInterface\DatasetInterface): bool`
     *   All quads for which the callable returns true are copied.
     * 
     * @param QuadCompareInterface|QuadIteratorInterface|QuadIteratorAggregateInterface|callable|null $filter
     * @return QuadIteratorInterface
     */
    public function getIterator(QuadCompareInterface | QuadIteratorInterface | QuadIteratorAggregateInterface | callable | null $filter = null): QuadIteratorInterface {
        $iter = $this->dataset->getIterator($filter);
        return new GenericQuadIterator($this->filterNode($iter));
    }

    private function filterNode(QuadIteratorInterface $quads): Generator {
        foreach ($quads as $i) {
            if ($this->node->equals($i->getSubject())) {
                yield $i;
            }
        }
    }
}
