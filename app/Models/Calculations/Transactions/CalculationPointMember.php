<?php

namespace App\Models\Calculations\Transactions;

use App\Models\Members\Member;
use App\Models\Orders\Production\OrderHeader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CalculationPointMember extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'calculation_point_members';
    protected $primaryKey = 'id';

    protected $casts = [
        'total_amount' => 'float',
        'total_amount_summary' => 'float',
        'ppv' => 'float',
        'pxv' => 'float',
        'pbv' => 'float',
        'prv' => 'float',
        'gpv' => 'float',
        'gxv' => 'float',
        'gbv' => 'float',
        'grv' => 'float',
        'pgpv' => 'float',
        'pgxv' => 'float',
        'pgbv' => 'float',
        'pgrv' => 'float',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'process_uuid',
        'start_date',
        'end_date',
        'member_uuid',
        'rank_uuid',
        'sponsor_uuid',
        'total_amount',
        'total_amount_summary',
        'ppv',
        'pxv',
        'pbv',
        'prv',
        'gpv',
        'gxv',
        'gbv',
        'grv',
        'pgpv',
        'pgxv',
        'pgbv',
        'pgrv',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Indicates if the IDs are UUID's.
     *
     * @var bool
     */
    public $incrementing = false;

    public function member()
    {
        return $this->belongsTo(
            Member::class,
            'member_uuid',
            'uuid'
        );
    }

    public function orders()
    {
        return $this->belongsTo(
            OrderHeader::class,
            'uuid',
            'uuid'
        );
    }
}
