<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TicketAutomationService;

class AutoCloseResolvedTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:auto-close-resolved';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically close resolved tickets after 7 days of no updates';

    protected TicketAutomationService $automationService;

    public function __construct(TicketAutomationService $automationService)
    {
        parent::__construct();
        $this->automationService = $automationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting auto-closure of resolved tickets...');
        
        $closedCount = $this->automationService->autoCloseResolvedTickets();
        
        if ($closedCount > 0) {
            $this->info("Successfully auto-closed {$closedCount} resolved tickets.");
        } else {
            $this->info('No resolved tickets found that need auto-closure.');
        }
        
        return Command::SUCCESS;
    }
}
