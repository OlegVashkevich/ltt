<?php

namespace Tests\Cron;

use LTT\Cron\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase {
    public function testConstructor(): void {
        $task = new Task();
        $this->assertObjectHasProperty('expression', $task);
        $this->assertObjectHasProperty('command', $task);
    }
    public function testParser(): void {
        $task = new Task();
        $task->parseTask('* * * * * '.PHP_BINARY.' test');
        $this->assertEquals('* * * * *', $task->expression);
        $this->assertEquals('test', $task->command);
    }
    public function testEveryMinute(): void {
        $task = new Task();
        $task->everyMinute();
        $this->assertEquals('* * * * *', $task->expression);
    }
    public function testEveryTwoMinutes(): void {
        $task = new Task();
        $task->everyTwoMinutes();
        $this->assertEquals('*/2 * * * *', $task->expression);
    }
    public function testEveryThreeMinutes(): void {
        $task = new Task();
        $task->everyThreeMinutes();
        $this->assertEquals('*/3 * * * *', $task->expression);
    }

    public function testEveryFourMinutes(): void {
        $task = new Task();
        $task->everyFourMinutes();
        $this->assertEquals('*/4 * * * *', $task->expression);
    }

    public function testEveryFiveMinutes(): void {
        $task = new Task();
        $task->everyFiveMinutes();
        $this->assertEquals('*/5 * * * *', $task->expression);
    }

    public function testEveryTenMinutes(): void {
        $task = new Task();
        $task->everyTenMinutes();
        $this->assertEquals('*/10 * * * *', $task->expression);
    }

    public function testEveryFifteenMinutes(): void {
        $task = new Task();
        $task->everyFifteenMinutes();
        $this->assertEquals('*/15 * * * *', $task->expression);
    }

    public function testEveryThirtyMinutes(): void {
        $task = new Task();
        $task->everyThirtyMinutes();
        $this->assertEquals('*/30 * * * *', $task->expression);
    }

    public function testHourly(): void {
        $task = new Task();
        $task->hourly();
        $this->assertEquals('0 * * * *', $task->expression);
    }

    public function testHourlyAtInt(): void {
        $task = new Task();
        $task->hourlyAt(6);
        $this->assertEquals('6 * * * *', $task->expression);
    }
    public function testHourlyAtArray(): void {
        $task = new Task();
        $task->hourlyAt([1,3,6]);
        $this->assertEquals('1,3,6 * * * *', $task->expression);
    }
    public function testHourlyAtString(): void {
        $task = new Task();
        $task->hourlyAt('1,3,5');
        $this->assertEquals('1,3,5 * * * *', $task->expression);
    }

    public function testEveryOddHourInt(): void {
        $task = new Task();
        $task->everyOddHour(6);
        $this->assertEquals('6 1-23/2 * * *', $task->expression);
    }
    public function testEveryOddHourArray(): void {
        $task = new Task();
        $task->everyOddHour([1,3,6]);
        $this->assertEquals('1,3,6 1-23/2 * * *', $task->expression);
    }
    public function testEveryOddHourString(): void {
        $task = new Task();
        $task->everyOddHour('1,3,5');
        $this->assertEquals('1,3,5 1-23/2 * * *', $task->expression);
    }
    public function testEveryTwoHoursInt(): void {
        $task = new Task();
        $task->everyTwoHours(6);
        $this->assertEquals('6 */2 * * *', $task->expression);
    }
    public function testEveryTwoHoursArray(): void {
        $task = new Task();
        $task->everyTwoHours([1,3,6]);
        $this->assertEquals('1,3,6 */2 * * *', $task->expression);
    }
    public function testEveryTwoHoursString(): void {
        $task = new Task();
        $task->everyTwoHours('1,3,5');
        $this->assertEquals('1,3,5 */2 * * *', $task->expression);
    }
    public function testEveryThreeHoursInt(): void {
        $task = new Task();
        $task->everyThreeHours(6);
        $this->assertEquals('6 */3 * * *', $task->expression);
    }
    public function testEveryThreeHoursArray(): void {
        $task = new Task();
        $task->everyThreeHours([1,3,6]);
        $this->assertEquals('1,3,6 */3 * * *', $task->expression);
    }
    public function testEveryThreeHoursString(): void {
        $task = new Task();
        $task->everyThreeHours('1,3,5');
        $this->assertEquals('1,3,5 */3 * * *', $task->expression);
    }
    public function testEveryFourHoursInt(): void {
        $task = new Task();
        $task->everyFourHours(6);
        $this->assertEquals('6 */4 * * *', $task->expression);
    }
    public function testEveryFourHoursArray(): void {
        $task = new Task();
        $task->everyFourHours([1,3,6]);
        $this->assertEquals('1,3,6 */4 * * *', $task->expression);
    }
    public function testEveryFourHoursString(): void {
        $task = new Task();
        $task->everyFourHours('1,3,5');
        $this->assertEquals('1,3,5 */4 * * *', $task->expression);
    }
    public function testEverySixHoursInt(): void {
        $task = new Task();
        $task->everySixHours(6);
        $this->assertEquals('6 */6 * * *', $task->expression);
    }
    public function testEverySixHoursArray(): void {
        $task = new Task();
        $task->everySixHours([1,3,6]);
        $this->assertEquals('1,3,6 */6 * * *', $task->expression);
    }
    public function testEverySixHoursString(): void {
        $task = new Task();
        $task->everySixHours('1,3,5');
        $this->assertEquals('1,3,5 */6 * * *', $task->expression);
    }
    public function testDaily(): void {
        $task = new Task();
        $task->daily();
        $this->assertEquals('0 0 * * *', $task->expression);
    }
    public function testDailyAt(): void {
        $task = new Task();
        $task->dailyAt('19:30');
        $this->assertEquals('30 19 * * *', $task->expression);
    }
    public function testTwiceDaily(): void {
        $task = new Task();
        $task->twiceDaily(11,23);
        $this->assertEquals('0 11,23 * * *', $task->expression);
    }
    public function testTwiceDailyAt(): void {
        $task = new Task();
        $task->twiceDailyAt(11,23,54);
        $this->assertEquals('54 11,23 * * *', $task->expression);
    }
    public function testWeekdays(): void {
        $task = new Task();
        $task->weekdays();
        $this->assertEquals('* * * * 1-5', $task->expression);
    }
    public function testWeekends(): void {
        $task = new Task();
        $task->weekends();
        $this->assertEquals('* * * * 6,0', $task->expression);
    }
    public function testMondays(): void {
        $task = new Task();
        $task->mondays();
        $this->assertEquals('* * * * 1', $task->expression);
    }
    public function testTuesdays(): void {
        $task = new Task();
        $task->tuesdays();
        $this->assertEquals('* * * * 2', $task->expression);
    }
    public function testWednesdays(): void {
        $task = new Task();
        $task->wednesdays();
        $this->assertEquals('* * * * 3', $task->expression);
    }
    public function testThursdays(): void {
        $task = new Task();
        $task->thursdays();
        $this->assertEquals('* * * * 4', $task->expression);
    }
    public function testFridays(): void {
        $task = new Task();
        $task->fridays();
        $this->assertEquals('* * * * 5', $task->expression);
    }
    public function testSaturdays(): void {
        $task = new Task();
        $task->saturdays();
        $this->assertEquals('* * * * 6', $task->expression);
    }
    public function testSundays(): void {
        $task = new Task();
        $task->sundays();
        $this->assertEquals('* * * * 0', $task->expression);
    }
    public function testWeekly(): void {
        $task = new Task();
        $task->weekly();
        $this->assertEquals('0 0 * * 0', $task->expression);
    }
    public function testWeeklyOnInt(): void {
        $task = new Task();
        $task->weeklyOn(6,'19:30');
        $this->assertEquals('30 19 * * 6', $task->expression);
    }
    public function testWeeklyOnArray(): void {
        $task = new Task();
        $task->weeklyOn([1,3,6],'19:30');
        $this->assertEquals('30 19 * * 1,3,6', $task->expression);
    }
    public function testWeeklyOnString(): void {
        $task = new Task();
        $task->weeklyOn('1,3,5','19:30');
        $this->assertEquals('30 19 * * 1,3,5', $task->expression);
    }
    public function testMonthly(): void {
        $task = new Task();
        $task->monthly();
        $this->assertEquals('0 0 1 * *', $task->expression);
    }
    public function testMonthlyOn(): void {
        $task = new Task();
        $task->monthlyOn(17,'19:30');
        $this->assertEquals('30 19 17 * *', $task->expression);
    }
    public function testTwiceMonthly(): void {
        $task = new Task();
        $task->twiceMonthly(7,17,'19:30');
        $this->assertEquals('30 19 7,17 * *', $task->expression);
    }
    public function testQuarterly(): void {
        $task = new Task();
        $task->quarterly();
        $this->assertEquals('0 0 1 1-12/3 *', $task->expression);
    }
    public function testQuarterlyOn(): void {
        $task = new Task();
        $task->quarterlyOn(17,'19:30');
        $this->assertEquals('30 19 17 1-12/3 *', $task->expression);
    }
    public function testYearly(): void {
        $task = new Task();
        $task->yearly();
        $this->assertEquals('0 0 1 1 *', $task->expression);
    }
    public function testYearlyOn(): void {
        $task = new Task();
        $task->yearlyOn(7,17,'19:30');
        $this->assertEquals('30 19 17 7 *', $task->expression);
    }
}