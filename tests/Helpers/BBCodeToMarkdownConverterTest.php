<?php
/**
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 * Copyright (C) 2019 - 2020 Jan Böhmer (https://github.com/jbtronics)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\BBCodeToMarkdownConverter;
use PHPUnit\Framework\TestCase;

class BBCodeToMarkdownConverterTest extends TestCase
{
    protected BBCodeToMarkdownConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new BBCodeToMarkdownConverter();
    }

    public function dataProvider(): array
    {
        return [
            ['[b]Bold[/b]', '**Bold**'],
            ['[i]Italic[/i]', '*Italic*'],
            ['[s]Strike[/s]', '<s>Strike</s>'],
            ['[url]https://foo.bar[/url]', '<https://foo.bar>'],
            ['[url=https://foo.bar]test[/url]', '[test](https://foo.bar)'],
            ['[center]Centered[/center]', '<div style="text-align:center">Centered</div>'],
            ['test no change', 'test no change'],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param $bbcode
     * @param $expected
     */
    public function testConvert($bbcode, $expected): void
    {
        $this->assertSame($expected, $this->converter->convert($bbcode));
    }
}
