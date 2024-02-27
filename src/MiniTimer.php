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

    private function __construct(string $logFile = 'timers.log')
    {
        $this->logFile = $logFile;
    }

    public function start(string $key): void
    {
        $startTime = microtime(true);
        $parentKey = end($this->taskStack) ?: null; // Obtient la clé de la tâche parente si elle existe, sinon null
        $this->taskStack[] = $key; // Ajoute la tâche actuelle à la pile

        if (!array_key_exists($key, $this->timers)) {
            $this->timers[$key] = ['time' => 0, 'start' => $startTime, 'parent' => $parentKey, 'children' => []];
            if ($parentKey) {
                $this->timers[$parentKey]['children'][] = $key; // Ajoute la tâche actuelle comme enfant de la tâche parente
            }
        } else {
            // Si la tâche est déjà commencée, réinitialisez simplement son heure de début
            $this->timers[$key]['start'] = $startTime;
        }
    }

    // Méthode pour obtenir l'instance de la classe
    public static function inst(string $logFile = 'timers.log'): self
    {
        if (self::$instance === null) {
            self::$instance = new self($logFile);
        }
        return self::$instance;
    }


    // Empêcher le clonage de l'instance
    private function __clone()
    {
    }

    // Empêcher la désérialisation de l'instance
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public function stop(string $key): void
    {
        if (!array_key_exists($key, $this->timers)) {
            return; // Si la clé n'existe pas, ne fait rien
        }

        $endTime = microtime(true);
        $this->timers[$key]['time'] += $endTime - $this->timers[$key]['start'];
        array_pop($this->taskStack); // Retire la dernière tâche de la pile
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
        echo self::CSS_STYLES . '<table class="minitimer_table">';
        foreach ($this->timers as $key => $timer) {
            if ($timer['time'] >= $min && $timer['parent'] === null) { // Affiche seulement les tâches de niveau supérieur
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
        $indent = str_repeat('---', $level).' '; // Indentation pour les sous-tâches
        $timeFormatted = $this->formatTime($timer['time']);
        $percentage = '';

        if ($parentTime !== null) {
            $percentage = ' (' . round(($timer['time'] / $parentTime) * 100) . '%)';
        }

        echo '<tr><td>' . $indent . $key . '</td><td class="time">' . $timeFormatted . $percentage . '</td></tr>';

        if (!empty($timer['children'])) {
            // Tri des enfants par temps décroissant avant affichage
            $sortedChildren = $this->sortChildrenByTime($timer['children']);
            foreach ($sortedChildren as $childKey) {
                $this->displayTask($childKey, $level + 1, $timer['time']);
            }
        }

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
