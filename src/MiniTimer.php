<?php

class MiniTimer
{

    private static $miniTimer;
    public $timers = array();
    public $points = array();

    public function start($key)
    {
        if (!array_key_exists($key, $this->timers)) {
            $this->timers[$key] = array();
            $this->timers[$key]['time'] = 0;
        }
        $this->timers[$key]['start'] = microtime(true);
    }

    public function stop($key)
    {
        $this->timers[$key]['time'] += microtime(true) - $this->timers[$key]['start'];
    }

    public function addPoint()
    {
        $this->points[] = array(
            'time' => microtime(true),
            'backtrace' => debug_backtrace()
        );
    }

    public static function inst()
    {
        if (!self::$miniTimer) {
            self::$miniTimer = new miniTimer();
        }

        return self::$miniTimer;
    }

    private function formatTime($time)
    {
        if ($time > 0.1) {
            return round($time, 2) . ' s';
        } else {
            return round($time * 1000) . ' ms';
        }
    }

    public function display($min = 0)
    {
        echo '<style>
                .minitimer_table { border-collapse:collapse; margin:20px 0; }
                .minitimer_table td { padding: 7px 15px;  border:1px solid #ccc; }
                .minitimer_table small { color: #666; }
                .minitimer_table .time { text-align:right; }
             </style>' .
            '<table class="minitimer_table">' . $this->displayTimers($min) . $this->displayPoints($min) . '</table>';
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
