<?php
/**
 * Copyright (C) 2011-2016 by Lars Strojny <lstrojny@php.net>
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
$base_path = dirname(dirname(__DIR__));
require_once("$base_path/src/functional/InvalidArgumentException.php");

/**
 * Extract a property from a collection of objects.
 *
 * @param Traversable|array $collection
 * @param string $propertyName
 * @return array
 */
function pluck($collection, $propertyName)
{
    FuncInvalidArgumentException::assertCollection($collection, __FUNCTION__, 1);
    FuncInvalidArgumentException::assertPropertyName($propertyName, __FUNCTION__, 2);
    $aggregation = array();
    foreach ($collection as $index => $element) {
        $value = null;
        if (is_object($element) && isset($element->{$propertyName})) {
            $value = $element->{$propertyName};
        } elseif ((is_array($element) || $element instanceof ArrayAccess) && isset($element[$propertyName])) {
            $value = $element[$propertyName];
        }
        $aggregation[$index] = $value;
    }
    return $aggregation;
}