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
        'p_pv' => 'float',
        'p_xv' => 'float',
        'p_bv' => 'float',
        'p_rv' => 'float',
        'g_pv' => 'float',
        'g_xv' => 'float',
        'g_bv' => 'float',
        'g_rv' => 'float',
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
        'p_pv',
        'p_xv',
        'p_bv',
        'p_rv',
        'g_pv',
        'g_xv',
        'g_bv',
        'g_rv',
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
