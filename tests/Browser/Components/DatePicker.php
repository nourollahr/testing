<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class DatePicker extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '.dp-cal.dp-flyout';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    public function selectDate(Browser $browser, $year, $month, $day)
    {
        $browser
            ->click('button.dp-cal-year')
            ->with('.dp-years.dp-submenu', function ($list) use ($year) {
                $list->press($year);
            })
            ->click('button.dp-cal-month')
            ->with('.dp-months.dp-submenu', function ($list) use ($month) {
                $list->press($month);
            })
            ->with('.dp-days', function ($list) use ($day) {
                $list->script("
                        $('button.dp-day:not(.dp-edge-day):contains($day)')
                        .first().click();
                    ");
            });
    }
    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
