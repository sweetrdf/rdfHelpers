<?php

/*
 * The MIT License
 *
 * Copyright 2021 zozlak.
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

use zozlak\RdfConstants as RDF;
use rdfInterface\NamedNode;
use rdfInterface\BlankNode;
use rdfInterface\Literal;
use rdfInterface\DefaultGraph;

/**
 * Description of Util
 *
 * @author zozlak
 */
class NtriplesUtil
{

    /**
     * Characters forbidden in n-triples literals according to
     * https://www.w3.org/TR/n-triples/#grammar-production-IRIREF
     *
     * @var string[]
     */
    private static $iriEscapeMap = array(
        "<"    => "\\u003C",
        ">"    => "\\u003E",
        '"'    => "\\u0022",
        "{"    => "\\u007B",
        "}"    => "\\u007D",
        "|"    => "\\u007C",
        "^"    => "\\u005E",
        "`"    => "\\u0060",
        "\\"   => "\\u005C",
        "\x00" => "\\u0030",
        "\x01" => "\\u0031",
        "\x02" => "\\u0032",
        "\x03" => "\\u0033",
        "\x04" => "\\u0034",
        "\x05" => "\\u0035",
        "\x06" => "\\u0036",
        "\x07" => "\\u0037",
        "\x08" => "\\u0038",
        "\x09" => "\\u0039",
        "\x0A" => "\\u0031",
        "\x0B" => "\\u0031",
        "\x0C" => "\\u0031",
        "\x0D" => "\\u0031",
        "\x0E" => "\\u0031",
        "\x0F" => "\\u0031",
        "\x10" => "\\u0031",
        "\x11" => "\\u0031",
        "\x12" => "\\u0031",
        "\x13" => "\\u0031",
        "\x14" => "\\u0032",
        "\x15" => "\\u0032",
        "\x16" => "\\u0032",
        "\x17" => "\\u0032",
        "\x18" => "\\u0032",
        "\x19" => "\\u0032",
        "\x1A" => "\\u0032",
        "\x1B" => "\\u0032",
        "\x1C" => "\\u0032",
        "\x1D" => "\\u0032",
        "\x1E" => "\\u0033",
        "\x1F" => "\\u0033",
        "\x20" => "\\u0033"
    );

    /**
     * Characters forbidden in n-triples literals according to
     * https://www.w3.org/TR/n-triples/#grammar-production-STRING_LITERAL_QUOTE
     * @var string[]
     */
    private static $literalEscapeMap = array(
        "\n" => '\\n',
        "\r" => '\\r',
        '"'  => '\\"',
        '\\' => '\\\\'
    );

    public static function escapeLiteral(string $str): string
    {
        return strtr($str, self::$literalEscapeMap);
    }

    public static function escapeIri(string $str): string
    {
        return strtr($str, self::$iriEscapeMap);
    }

    public static function serializeIri(NamedNode | BlankNode $res): string
    {
        if ($res instanceof DefaultGraph) {
            return '';
        }
        $escaped = self::escapeIri((string) $res->getValue());
        if (substr($res, 0, 2) == '_:') {
            return $escaped;
        } else {
            return "<$escaped>";
        }
    }

    public static function serializeLiteral(Literal $literal): string
    {
        $langtype = '@' . $literal->getLang();
        if ($langtype === '@') {
            $langtype = $literal->getDatatype();
            $langtype = $langtype == RDF::XSD_STRING ?: '^^<' . self::escapeIri($literal->getDatatype()) . '>';
        }
        return self::escapeLiteral((string) $literal->getValue()) . $langtype;
    }

    public static function serialize(NamedNode | BlankNode | Literal $term): string
    {
        if ($term instanceof Literal) {
            return self::serializeLiteral($term);
        } else {
            return self::serializeIri($term);
        }
    }
}
