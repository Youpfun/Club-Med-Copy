<?php

namespace App\Livewire\Pulse;

use Livewire\Component;

class PingCard extends Component
{
    public $pingTime;
    // L'IP de votre base de données distante
    public $host = '51.83.36.122'; 

    public function mount()
    {
        $this->pingTime = $this->getPingTime($this->host);
    }

    private function getPingTime($host)
    {
        // Commande adaptée pour Windows (-n 1). 
        // Si vous passez sur Linux plus tard, remplacez "-n" par "-c".
        $output = shell_exec("ping -n 1 " . $host);

        // Regex pour capturer le temps (ms) dans la réponse Windows ou Linux
        // Windows renvoie souvent "Temps=34ms" ou "time=34ms"
        if (preg_match('/(temps|time)[=<](\d+)(ms| ms)/i', $output, $matches)) {
            return $matches[2] . ' ms';
        }

        return 'N/A';
    }

    public function render()
    {
        return view('livewire.pulse.ping-card');
    }
}