<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TransferItemsModel;

class RejectExpiredTransfers extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'transfers:reject-expired';
    protected $description = 'Reject transfers older than 24 hours';

    public function run(array $params)
    {
        $transferModel = new TransferItemsModel();
        
        $expiredTransfers = $transferModel
            ->where('order_status_id', 1)
            ->where('created_at <', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->findAll();

        if (empty($expiredTransfers)) {
            CLI::write('No expired transfers found.', 'green');
            log_message('info', 'Cron: No expired transfers');
            return;
        }

        $count = 0;
        foreach ($expiredTransfers as $transfer) {
            $updated = $transferModel->update($transfer->transfer_item_id, [
                'order_status_id' => 3,
                'updated_at' => date('Y-m-d H:i:s'),
                'note' => ($transfer->note ? $transfer->note . ' | ' : '') . 'تم الرفض تلقائياً بسبب انتهاء المهلة'
            ]);
            
            if ($updated) {
                $count++;
            }
        }

        CLI::write("Rejected {$count} transfers", 'green');
        log_message('info', "Cron: Rejected {$count} expired transfers");
    }
}