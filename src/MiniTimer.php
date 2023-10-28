<?php

class MiniTimer
{

    private array $timers = [];
    private array $points = [];
    private const CSS_STYLES = '<style>
        .minitimer_table { border-collapse:collapse; margin:20px 0; }
        .minitimer_table td { padding: 7px 15px;  border:1px solid #ccc; }
        .minitimer_table small { color: #666; }
        .minitimer_table .time { text-align:right; }
    </style>';

    public function start(string $key): void
    {
        if (!array_key_exists($key, $this->timers)) {
            $this->timers[$key] = ['time' => 0];
        }
        $this->timers[$key]['start'] = microtime(true);
    }

    public function stop(string $key): void
    {
        $this->timers[$key]['time'] += microtime(true) - $this->timers[$key]['start'];
    }

    public function addPoint(): void
    {
        $this->points[] = [
            'time' => microtime(true),
            'backtrace' => debug_backtrace()
        ];
    }

    private function formatTime(float $time): string
    {
        return $time > 1 ? round($time, 3) . ' s' : round($time * 1000) . ' ms';
    }

    public function display($min = 0)
    {
        echo self::CSS_STYLES . '<table class="minitimer_table">' . $this->displayTimers($min) . $this->displayPoints($min) . '</table>';
    }

    private function displayTimers($min = 0)
    {
        if (empty($this->timers)) {
            return false;
        }

        uasort($this->timers, function ($a, $b) {
            return $a['time'] < $b['time'];
        });
        $tableRow = '';
        foreach ($this->timers as $key => $timer) {
            if ($timer['time'] >= $min) {
                $tableRow .= '<tr><td>' . $key . '</td><td class="time">' . self::formatTime($timer['time']) . '</td></tr>';
            }
        }

        return $tableRow;
    }

    private function displayPoints($min = 0)
    {
        if (empty($this->points)) {
            return false;
        }

        $isFirst = true;
        $tableRow = '';
        $last_point = array();
        foreach ($this->points as $point) {
            if ($isFirst) {
                $isFirst = false;
            } else {
                $time = $point['time'] - $last_point['time'];
                if ($time >= $min) {
                    $tableRow .= '<tr>
                        <td>
                            Between <small>' . $last_point['backtrace'][0]['file'] . '</small> line ' . $last_point['backtrace'][0]['line'] . '
                            and <small>' . $point['backtrace'][0]['file'] . '</small> line ' . $point['backtrace'][0]['line'] . '
                        </td>
                        <td class="time">' . self::formatTime($time) . '</td>
                    </tr>';
                }
            }
            $last_point = $point;
        }

        return $tableRow;
    }
}
