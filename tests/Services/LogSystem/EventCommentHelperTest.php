<?php
/*
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 *  Copyright (C) 2019 - 2022 Jan Böhmer (https://github.com/jbtronics)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

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

namespace App\Tests\Services\LogSystem;

use App\Services\LogSystem\EventCommentHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventCommentHelperTest extends WebTestCase
{
    /**
     * @var EventCommentHelper
     */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        //Get a service instance.
        self::bootKernel();
        $this->service = self::getContainer()->get(EventCommentHelper::class);
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->service->getMessage());
        $this->assertFalse($this->service->isMessageSet());
    }

    public function testClearMessage(): void
    {
        $this->service->setMessage('Test');
        $this->assertTrue($this->service->isMessageSet());
        $this->service->clearMessage();
        $this->assertFalse($this->service->isMessageSet());
    }

    public function testGetSetMessage(): void
    {
        $this->service->setMessage('Test');
        $this->assertSame('Test', $this->service->getMessage());
    }

    public function testIsMessageSet(): void
    {
        $this->service->setMessage('Test');
        $this->assertTrue($this->service->isMessageSet());
        $this->service->clearMessage();
        $this->assertFalse($this->service->isMessageSet());
    }
}
