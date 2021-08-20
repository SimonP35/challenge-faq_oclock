<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceService
{
    private $maintenanceDate;

    private $maintenanceScheduled;

    public function __construct(bool $maintenanceScheduled, string $maintenanceDate)
    {
        $this->maintenanceScheduled = $maintenanceScheduled;
        $this->maintenanceDate = $maintenanceDate;
    }

    public function scheduleMaintenance(ResponseEvent $event)
    {
        if ($this->maintenanceScheduled) {

            $eventContent = $event->getResponse()->getContent();
            $newEventContent = str_replace('<body>', "<body><div class=\"alert alert-danger\">Maintenance prévue {$this->maintenanceDate}</div>", $eventContent);
            return $event->getResponse()->setContent($newEventContent);  
        }
    }
}