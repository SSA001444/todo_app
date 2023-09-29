<?php

namespace Tests\Browser;

use App\Http\Kernel;
use Laravel\Dusk\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;


class DropDownIntegrationTest extends DuskTestCase
{
    /**
     *
     *A Dusk test example.
     */
    public function testDropDown(): void
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/todos');
            $browser->assertSee('Task1');
            $browser->assertSee('Task2');
            $browser->assertSee('Task3');
            $browser->script("$('.task-item:first-child').simulateDragDrop({ dropTarget: '.task-item:nth-child(2)' });");

            $browser->assertSeeIn('.task-item:nth-child(1)', 'Task 2');
            $browser->assertSeeIn('.task-item:nth-child(2)', 'Task 1');
            $browser->assertSeeIn('.task-item:nth-child(3)', 'Task 3');
        });
    }
}
