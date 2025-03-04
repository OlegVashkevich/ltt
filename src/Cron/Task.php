<?php

namespace LTT\Cron;

use Stringable;

class Task implements Stringable {
    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    /**
     * @param  string  $command команда shell или php скрипт
     * @param  string  $expression Выражение cron, представляющее частоту выполнения команды.
     */

    public function __construct(public string $command = '', public string $expression = '* * * * *') {}

    public function __toString(): string
    {
        return $this->expression.$this->getSeparator().$this->command;
    }

    /**
     * Парсит задачу полученную из crontab
     *
     * @param  string  $task
     * @return $this
     */
    public function parseTask(string $task): static
    {
        $data = explode($this->getSeparator(), $task);
        $this->expression = $data[0];
        $this->command = $data[1];

        return $this;
    }

    public function getSeparator(): string
    {
        return ' ' . PHP_BINARY . ' ';
    }

    /**
     * Устанавливает выражение cron, представляющее частоту выполнения команды.
     *
     * @param  string  $expression
     * @return $this
     */
    public function cron(string $expression): static
    {
        $this->expression = $expression;

        return $this;
    }


    /**
     * Запуск команды каждую минуту
     *
     * @return $this
     */
    public function everyMinute(): static
    {
        return $this->spliceIntoPosition(1, '*');
    }

    /**
     * Запуск команды каждые 2 минуты
     *
     * @return $this
     */
    public function everyTwoMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/2');
    }

    /**
     * Запуск команды каждые 3 минуты
     *
     * @return $this
     */
    public function everyThreeMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/3');
    }

    /**
     * Запуск команды каждые 4 минуты
     *
     * @return $this
     */
    public function everyFourMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/4');
    }

    /**
     * Запуск команды каждые 5 минуты
     *
     * @return $this
     */
    public function everyFiveMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/5');
    }

    /**
     * Запуск команды каждых 10 минут
     *
     * @return $this
     */
    public function everyTenMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/10');
    }

    /**
     * Запуск команды каждых 15 минут
     *
     * @return $this
     */
    public function everyFifteenMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/15');
    }

    /**
     * Запуск команды каждые пол часа
     *
     * @return $this
     */
    public function everyThirtyMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/30');
    }

    /**
     * Запуск команды каждый час
     *
     * @return $this
     */
    public function hourly(): static
    {
        return $this->spliceIntoPosition(1, "0");
    }

    /**
     * Команда будет запускаться каждый час с заданным смещением в течение часа
     *
     * @param  int<0, 59>|array<int,int<0, 59>>|string  $offset
     * @return $this
     */
    public function hourlyAt(int|array|string $offset): static
    {
        return $this->hourBasedSchedule($offset, '*');
    }

    /**
     * Команда будет запускаться каждый нечетный час
     *
     * @param  int<0, 59>|array<int,int<0, 59>>|string  $offset
     * @return $this
     */
    public function everyOddHour(array|int|string $offset = 0): static
    {
        return $this->hourBasedSchedule($offset, '1-23/2');
    }

    /**
     * Команда будет запускаться каждые 2 часа
     *
     * @param  int<0, 59>|string|array<int,int<0, 59>>  $offset
     * @return $this
     */
    public function everyTwoHours(array|int|string $offset = 0): static
    {
        return $this->hourBasedSchedule($offset, '*/2');
    }

    /**
     * Команда будет запускаться каждые 3 часа
     *
     * @param  int<0, 59>|string|array<int,int<0, 59>>  $offset
     * @return $this
     */
    public function everyThreeHours(array|int|string $offset = 0): static
    {
        return $this->hourBasedSchedule($offset, '*/3');
    }

    /**
     * Команда будет запускаться каждые 4 часа
     *
     * @param  int<0, 59>|string|array<int,int<0, 59>>  $offset
     * @return $this
     */
    public function everyFourHours(array|int|string $offset = 0): static
    {
        return $this->hourBasedSchedule($offset, '*/4');
    }

    /**
     * Команда будет запускаться каждые 6 часов
     *
     * @param  int<0, 59>|string|array<int,int<0, 59>>  $offset
     * @return $this
     */
    public function everySixHours(array|int|string $offset = 0): static
    {
        return $this->hourBasedSchedule($offset, '*/6');
    }

    /**
     * Команда будет запускаться ежедневно
     *
     * @return $this
     */
    public function daily(): static
    {
        return $this->hourBasedSchedule(0, 0);
    }

    /**
     * Команда будет запускаться ежедневно в указанное время (10:00, 19:30 и т.д.).
     *
     * @param  string  $time
     * @return $this
     */
    public function dailyAt(string $time): static
    {
        $segments = explode(':', $time);

        return $this->hourBasedSchedule(
            count($segments) === 2 ? $segments[1] : '0',
            $segments[0]
        );
    }

    /**
     * Команда будет запускаться дважды в день
     *
     * @param  int<0, 23>  $first
     * @param  int<0, 23>  $second
     * @return $this
     */
    public function twiceDaily(int $first = 1, int $second = 13): static
    {
        return $this->twiceDailyAt($first, $second);
    }

    /**
     * Команда будет запускаться дважды в день с заданным смещением в минутах
     *
     * @param  int<0, 23>  $first
     * @param  int<0, 23>  $second
     * @param  int<0, 59>  $offset
     * @return $this
     */
    public function twiceDailyAt(int $first = 1, int $second = 13, int $offset = 0): static
    {
        $hours = $first.','.$second;

        return $this->hourBasedSchedule($offset, $hours);
    }

    /**
     * Команда будет запускаться в рабочие дни
     *
     * @return $this
     */
    public function weekdays(): static
    {
        return $this->days(self::MONDAY.'-'.self::FRIDAY);
    }

    /**
     * Команда будет запускаться только по выходным
     *
     * @return $this
     */
    public function weekends(): static
    {
        return $this->days(self::SATURDAY.','.self::SUNDAY);
    }

    /**
     * Команда будет запускаться в понедельник
     *
     * @return $this
     */
    public function mondays(): static
    {
        return $this->days(self::MONDAY);
    }

    /**
     * Команда будет запускаться во вторник
     *
     * @return $this
     */
    public function tuesdays(): static
    {
        return $this->days(self::TUESDAY);
    }

    /**
     * Команда будет запускаться в среду
     *
     * @return $this
     */
    public function wednesdays(): static
    {
        return $this->days(self::WEDNESDAY);
    }

    /**
     * Команда будет запускаться в четверг
     *
     * @return $this
     */
    public function thursdays(): static
    {
        return $this->days(self::THURSDAY);
    }

    /**
     * Команда будет запускаться в пятницу
     *
     * @return $this
     */
    public function fridays(): static
    {
        return $this->days(self::FRIDAY);
    }

    /**
     * Команда будет запускаться в субботу
     *
     * @return $this
     */
    public function saturdays(): static
    {
        return $this->days(self::SATURDAY);
    }

    /**
     * Команда будет запускаться в воскресенье
     *
     * @return $this
     */
    public function sundays(): static
    {
        return $this->days(self::SUNDAY);
    }

    /**
     * Команда будет запускаться еженедельно
     *
     * @return $this
     */
    public function weekly(): static
    {
        return $this->spliceIntoPosition(1, "0")
            ->spliceIntoPosition(2, "0")
            ->spliceIntoPosition(5, "0");
    }

    /**
     * Команда будет запускаться еженедельно в определенный день и время.
     *
     * @param  int<0, 6>|string|array<int,int<0, 6>>  $dayOfWeek
     * @param  string  $time
     * @return $this
     */
    public function weeklyOn(int|string|array $dayOfWeek, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->days($dayOfWeek);
    }

    /**
     * Команда будет запускаться ежемесячно
     *
     * @return $this
     */
    public function monthly(): static
    {
        return $this->spliceIntoPosition(1, "0")
            ->spliceIntoPosition(2, "0")
            ->spliceIntoPosition(3, "1");
    }

    /**
     * Команда будет запускаться ежемесячно в определенный день и время.
     *
     * @param  int<1, 31>  $dayOfMonth
     * @param  string  $time
     * @return $this
     */
    public function monthlyOn(int $dayOfMonth = 1, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, (string) $dayOfMonth);
    }

    /**
     * Команда будет запускаться дважды в месяц в определенный день и время.
     *
     * @param  int<1, 31>  $first
     * @param  int<1, 31>  $second
     * @param  string  $time
     * @return $this
     */
    public function twiceMonthly(int $first = 1, int $second = 16, string $time = '0:0'): static
    {
        $daysOfMonth = $first.','.$second;

        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $daysOfMonth);
    }

    /**
     * Команда будет запускаться ежеквартально
     *
     * @return $this
     */
    public function quarterly(): static
    {
        return $this->spliceIntoPosition(1, "0")
            ->spliceIntoPosition(2, "0")
            ->spliceIntoPosition(3, "1")
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * Команда будет запускаться ежеквартально в определенный день и время
     *
     * @param  int  $dayOfQuarter
     * @param  string  $time
     * @return $this
     */
    public function quarterlyOn(int $dayOfQuarter = 1, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, (string) $dayOfQuarter)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * Команда будет запускаться ежегодно
     *
     * @return $this
     */
    public function yearly(): static
    {
        return $this->spliceIntoPosition(1, "0")
            ->spliceIntoPosition(2, "0")
            ->spliceIntoPosition(3, "1")
            ->spliceIntoPosition(4, "1");
    }

    /**
     * Команда будет запускаться ежегодно в определенный месяц, день и время
     *
     * @param  int  $month
     * @param  int<1, 31>|string  $dayOfMonth
     * @param  string  $time
     * @return $this
     */
    public function yearlyOn(int $month = 1, int|string $dayOfMonth = 1, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, (string) $dayOfMonth)
            ->spliceIntoPosition(4, (string) $month);
    }

    /**
     * Укажите дни недели, в которые должна выполняться команда
     *
     * @param  int<0, 6>|string|array<int,int<0, 6>>  $days
     * @return $this
     */
    public function days(int|string|array $days): static
    {
        $days = is_array($days) ? $days : func_get_args();

        return $this->spliceIntoPosition(5, implode(',', $days));
    }

    /**
     * Запуск команды в указанные минуты и часы
     *
     * @param  int<0, 59>|array<int, int<0, 59>>|string  $minutes
     * @param  int<0, 23>|array<int, int<0, 23>>|string  $hours
     * @return $this
     */
    protected function hourBasedSchedule(int|array|string $minutes, int|array|string $hours): static
    {
        $minutes = is_array($minutes) ? implode(',', $minutes) : $minutes;

        $hours = is_array($hours) ? implode(',', $hours) : $hours;

        return $this->spliceIntoPosition(1, (string) $minutes)
            ->spliceIntoPosition(2, (string) $hours);
    }

    /**
     * Вставляет заданное значение в заданную позицию выражения
     *
     * @param  int  $position
     * @param  string  $value
     * @return $this
     */
    protected function spliceIntoPosition(int $position, string $value): static
    {
        $segments = preg_split("/\s+/", $this->expression);
        if($segments) {
            $segments[$position - 1] = $value;
            return $this->cron(implode(' ', $segments));
        }

        return $this->cron($this->expression);
    }
}