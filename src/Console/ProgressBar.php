<?php

namespace LTT\Console;

class ProgressBar
{
    public function __construct(
        private readonly int $total = 100,
        private int $from = 0,
        private readonly int $size = 50,
    ) {}

    public function next(): string
    {
        $this->from++;
        if ($this->from > $this->total) {
            return '';
        }
        usleep(10000);
        $out = '';
        // first call must have $current=0,
        // otherwise you'll delete some last
        // part of your app output

        $text = ' '.$this->from.' / '.$this->total;

        $total_size = $this->size + 7 + strlen($text);

        // if it's not first go, remove the previous bar
        if ($this->from > 0) {
            for ($place = $total_size; $place > 0; $place--) {
                // echo a backspace (hex:08) to remove the previous character
                $out .= "\x08";
            }
        }

        // output the progress bar as it should be
        for ($place = 0; $place <= $this->size; $place++) {
            // output green spaces if we're finished through this point
            // or grey spaces if not
            if ($place <= ($this->from / $this->total * $this->size)) {
                $out .= "\033[42m \033[0m";
            } else {
                $out .= "\033[47m \033[0m";
            }
        }

        $out .= $text;

        if ($this->from == $this->total) {
            $out .= PHP_EOL;
        }
        return $out;
    }

    public function __invoke(): ProgressBar
    {
        for ($x = $this->from; $x <= $this->total; $x++) {
            echo $this->next();
        }
        return $this;
    }
}