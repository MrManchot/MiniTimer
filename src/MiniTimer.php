<?php

class MiniTimer
{
    private static $instance = null;
    private array $timers = [];
    private array $taskStack = [];
    private const CSS_STYLES = '<style>
        .minitimer_table { border-collapse:collapse; margin:20px 0; }
        .minitimer_table td { padding: 7px 15px;  border:1px solid #ccc; }
        .minitimer_table small { color: #666; }
        .minitimer_table .time { text-align:right; }
    </style>';
    private string $logFile;
    private float $min_delay_display = 0;

    private function __construct(string $logFile = 'timers.log')
    {
        $this->logFile = $logFile;
    }

    public function start(string $key): void
    {
        $startTime = microtime(true);
        $parentKey = end($this->taskStack) ?: null;
        $this->taskStack[] = $key;

        if (!array_key_exists($key, $this->timers)) {
            $this->timers[$key] = ['time' => 0, 'start' => $startTime, 'parent' => $parentKey, 'children' => []];
            if ($parentKey) {
                $this->timers[$parentKey]['children'][] = $key;
            }
        } else {
            $this->timers[$key]['start'] = $startTime;
        }
    }

    public static function inst(string $logFile = 'timers.log'): self
    {
        if (self::$instance === null) {
            self::$instance = new self($logFile);
        }
        return self::$instance;
    }

    public function stop(string $key): void
    {
        if (!array_key_exists($key, $this->timers)) {
            return;
        }

        $endTime = microtime(true);
        $this->timers[$key]['time'] += $endTime - $this->timers[$key]['start'];
        array_pop($this->taskStack);
    }

    private function formatTime(float $time): string
    {
        return $time > 1 ? round($time, 1) . ' s' : round($time * 1000) . ' ms';
    }

    public function display(float $min = 0): void
    {
        $this->min_delay_display = $min;

        uasort($this->timers, function ($a, $b) {
            return $b['time'] <=> $a['time'];
        });
        
        echo self::CSS_STYLES . '<table class="minitimer_table">';
        foreach ($this->timers as $key => $timer) {
            if ($timer['parent'] === null) {
                $this->displayTask($key, 0);
            }
        }
        echo '</table>';
    }

    private function sortChildrenByTime(array $children): array
    {
        usort($children, function ($a, $b) {
            return $this->timers[$b]['time'] <=> $this->timers[$a]['time'];
        });
        return $children;
    }


    private function displayTask(string $key, int $level, ?float $parentTime = null): void
    {
        $timer = $this->timers[$key];
        if ($timer['time'] > $this->min_delay_display) {
            $indent = str_repeat('»»» ', $level) . ' ';
            $timeFormatted = $this->formatTime($timer['time']);
            $percentage = '';

            if ($parentTime !== null) {
                $percentage = ' <small>(' . round(($timer['time'] / $parentTime) * 100) . '%)</small>';
            }

            echo '<tr><td>' . $indent . $key . '</td><td class="time">' . $timeFormatted . $percentage . '</td></tr>';

            if (!empty($timer['children'])) {
                $sortedChildren = $this->sortChildrenByTime($timer['children']);
                foreach ($sortedChildren as $childKey) {
                    $this->displayTask($childKey, $level + 1, $timer['time']);
                }
            }
        }
    }



    public function save(): void
    {
        $dataToSave = [];

        foreach ($this->timers as $key => $timer) {
            $dataToSave[] = [
                'name' => $key,
                'time' => $timer['time']
            ];
        }

        file_put_contents($this->logFile, json_encode($dataToSave) . PHP_EOL, FILE_APPEND);
    }

    public function displayTotal(): void
    {
        $mergedTimers = [];

        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $timers = json_decode($line, true);
            foreach ($timers as $timer) {
                $name = $timer['name'];
                $time = $timer['time'];
                if (!isset($mergedTimers[$name])) {
                    $mergedTimers[$name] = 0;
                }
                $mergedTimers[$name] += $time;
            }
        }

        arsort($mergedTimers);

        echo self::CSS_STYLES . '<table class="minitimer_table">';
        foreach ($mergedTimers as $name => $time) {
            echo '<tr><td>' . $name . '</td><td>' . $this->formatTime($time) . '</td></tr>';
        }
        echo '</table>';
    }
}
