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
    private string $logFile;

    public function __construct(string $logFile = 'timers.log')
    {
        $this->logFile = $logFile;
    }

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

    public function display(float $min = 0): void
    {
        echo self::CSS_STYLES . '<table class="minitimer_table">' . $this->displayTimers($min) . $this->displayPoints($min) . '</table>';
    }

    private function displayTimers(float $min = 0): string
    {
        if (empty($this->timers)) {
            return '';
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

    private function displayPoints(float $min = 0): string
    {
        if (empty($this->points)) {
            return '';
        }

        $tableRow = '';
        $last_point = null;
        foreach ($this->points as $point) {
            if ($last_point) {
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

    public function save(): void
    {
        $dataToSave = [];

        // Sauvegarder les timers
        foreach ($this->timers as $key => $timer) {
            $dataToSave[] = [
                'name' => $key,
                'time' => $timer['time']
            ];
        }

        // Écrire les données dans un fichier au format JSON
        file_put_contents($this->logFile, json_encode($dataToSave) . PHP_EOL, FILE_APPEND);
    }

    public function displayTotal(): void
    {
        $mergedTimers = [];

        // Lire le fichier de log
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

        // Trier les timers du plus long au moins long
        arsort($mergedTimers);

        // Afficher les résultats
        echo self::CSS_STYLES . '<table class="minitimer_table">';
        foreach ($mergedTimers as $name => $time) {
            echo '<tr><td>' . $name . '</td><td>' . $this->formatTime($time) . '</td></tr>';
        }
        echo '</table>';
    }
}
