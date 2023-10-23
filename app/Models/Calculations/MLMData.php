<?php

namespace App\Models\Calculations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MLMData extends Model
{
    use HasFactory;

    protected $table = 'mlm_data';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'parent_id',
        'points',
        'akumulasi_points'
    ];

    public function sponsor()
    {
        return $this->belongsTo(Member::class, 'parent_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(MLMData::class, 'parent_id', 'id');
    }

    public function calculateAccumulatedPoints()
    {
        $accumulatedPoints = $this->points;

        if ($this->children->count() > 0) {
            foreach ($this->children as $child) {
                $accumulatedPoints += $child->calculateAccumulatedPoints();
            }
        }

        return $accumulatedPoints;
    }

    public function calculateAccumulatedPoints_V2()
    {
        $accumulatedPoints = $this->points;

        foreach ($this->children as $child) {
            $accumulatedPoints += $child->calculateAccumulatedPoints_V2();
        }

        return $accumulatedPoints;
    }

    public static function getMlmDataWithAccumulatedPoints()
    {
        $results = self::with('children')->get();

        $formattedResults = [];

        foreach ($results as $result) {
            $formattedResults[] = [
                'id' => $result->id,
                'parent_id' => $result->parent_id,
                'points' => $result->points,
                'akumulasi_points' => $result->calculateAccumulatedPoints_V2(),
            ];
        }

        return $formattedResults;
    }
}
