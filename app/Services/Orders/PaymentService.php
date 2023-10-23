<?php

use app\Libraries\Core;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Orders\Temporary\OrderHeaderTemp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function paid($request)
    {
        $orders = $request->all();

        try {
            DB::beginTransaction();
            if (Auth::check()) {
                $user = Auth::user();
            }

            $status = 1;

            foreach ($orders as $orderData) {
                $orderHeaderTemp = OrderHeaderTemp::lockForUpdate()
                    ->where(
                        'uuid',
                        $orderData['uuid']
                    )->firstOrFail();

                $orderHeaderTemp->update([
                    'status' => $status,
                    // 'updated_by' => $user->uuid,
                ]);
            }

            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Order fail to created. == ' . $e->getMessage(),
                [],
                FALSE,
                500
            );
        }
        return 'test';
    }
}
