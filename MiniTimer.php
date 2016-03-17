<?php


class MiniTimer {

    private static $miniTimer;
    public $timers = array();
    public $points = array();

    public function start($key) {
        if(!array_key_exists($key, $this->timers)) {
            $this->timers[$key] = array();
            $this->timers[$key]['time'] = 0;
        }
        $this->timers[$key]['start'] = microtime(true);
    }

    public function stop($key) {
        $this->timers[$key]['time'] += microtime(true) - $this->timers[$key]['start'];
    }

    public function addPoint() {
        $this->points[] = array(
            'time' => microtime(true),
            'backtrace' => debug_backtrace()
        );
    }

    public static function inst() {
        if (!self::$miniTimer)
            self::$miniTimer= new miniTimer();
        return self::$miniTimer;
    }

    private function formatTime($time)
    {
        if($time > 0.1) {
            return round($time, 2).' s';
        } else {
            return round($time * 1000).' ms';
        }
    }

    public function display()
    {
        echo '<style>.minitimer_table td { padding: 5px;  border:1px solid #ccc; }</style>'.$this->displayTimers().$this->displayPoints();
    }

    private function displayTimers() {
        if(empty($this->timers))
            return false;

        uasort($this->timers, function($a, $b) { return $a['time'] < $b['time']; });
        $tableRow = '';
        foreach($this->timers as $key => $timer) {
            $tableRow .= '<tr><td>'.$key.'</td><td>'.self::formatTime($timer['time']).'</td></tr>';
        }
        return '<table class="table">'.$tableRow.'</table>';
    }

    private function displayPoints() {
        if(empty($this->points))
            return false;

        $isFirst = true;
        $tableRow = '';
        foreach($this->points as $point) {
            if($isFirst) {
                $isFirst = false;
            } else {
                $time = $last_point['time'] - $point['time'];
                $tableRow .= '<tr>
                    <td>'.$last_point['backtrace'][0]['file'].' '.$last_point['backtrace'][0]['line'].'</td>
                    <td>'.$point['backtrace'][0]['file'].' '.$point['backtrace'][0]['line'].'</td>
                    <td>'.self::formatTime($time).'</td>
                </tr>';
            }
            $last_point = $point;
        }
        return '<table class="minitimer_table">'.$tableRow.'</table>';
    }
}
