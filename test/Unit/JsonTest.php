<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Format\Format;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Json
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodedException
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 */
final class JsonTest extends Framework\TestCase
{
    use Helper;

    public function testFromEncodedRejectsInvalidEncoded(): void
    {
        $string = self::faker()->realText();

        $this->expectException(Exception\InvalidJsonEncodedException::class);

        Json::fromEncoded($string);
    }

    /**
     * @dataProvider provideEncoded
     */
    public function testFromEncodedReturnsJson(string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        self::assertSame($encoded, $json->__toString());
        self::assertSame($encoded, $json->encoded());
        self::assertSame($encoded, \json_encode($json->decoded()));

        $format = Format::fromJson($json);

        self::assertSame($format->jsonEncodeOptions()->value(), $json->format()->jsonEncodeOptions()->value());
        self::assertSame($format->indent()->__toString(), $json->format()->indent()->__toString());
        self::assertSame($format->newLine()->__toString(), $json->format()->newLine()->__toString());
        self::assertSame($format->hasFinalNewLine(), $json->format()->hasFinalNewLine());
    }

    /**
     * @return \Generator<array<null|array|bool|float|int|string>>
     */
    public function provideEncoded(): \Generator
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
