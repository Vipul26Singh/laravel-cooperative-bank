<?php
namespace App\Listeners;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LogAuditTrail
{
    public function handle(object $event): void
    {
        $modelClass = null;
        $modelId = null;
        $action = class_basename($event);

        // Extract model from event by reflection
        $props = get_object_vars($event);
        foreach ($props as $prop) {
            if ($prop instanceof \Illuminate\Database\Eloquent\Model) {
                $modelClass = get_class($prop);
                $modelId = $prop->getKey();
                break;
            }
        }

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model_type' => $modelClass,
            'model_id'   => $modelId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'branch_id'  => session('branch_id'),
        ]);
    }
}
