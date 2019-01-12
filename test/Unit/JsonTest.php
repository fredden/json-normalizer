<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\Exception;
use Localheinz\Json\Normalizer\Format\Format;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class JsonTest extends Framework\TestCase
{
    use Helper;

    public function testFromEncodedRejectsInvalidEncoded(): void
    {
        $string = $this->faker()->realText();

        $this->expectException(Exception\InvalidJsonEncodedException::class);

        Json::fromEncoded($string);
    }

    /**
     * @dataProvider providerEncoded
     *
     * @param string $encoded
     */
    public function testFromEncodedReturnsJson(string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        self::assertInstanceOf(Json::class, $json);
        self::assertSame($encoded, $json->__toString());
        self::assertSame($encoded, $json->encoded());
        self::assertSame($encoded, \json_encode($json->decoded()));

        $format = Format::fromJson($json);

        self::assertSame($format->jsonEncodeOptions()->value(), $json->format()->jsonEncodeOptions()->value());
        self::assertSame($format->indent()->__toString(), $json->format()->indent()->__toString());
        self::assertSame($format->newLine()->__toString(), $json->format()->newLine()->__toString());
        self::assertSame($format->hasFinalNewLine(), $json->format()->hasFinalNewLine());
    }

    public function providerEncoded(): \Generator
    {
        $values = [
            'array-indexed' => [
                'foo',
                'bar',
            ],
            'array-associative' => [
                'foo' => 'bar',
            ],
            'bool-false' => false,
            'bool-true' => true,
            'float' => 3.14,
            'int' => 9000,
            'null' => null,
            'string' => 'foo',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                \json_encode($value),
            ];
        }
    }
}