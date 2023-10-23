<?php

namespace App\Services\Members;

use app\Libraries\Core;
use App\Models\Members\Member;

class MemberGenealogyService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function getGenealogy($uuid, $type = 'placement')
    {
        if (!in_array($type, ['placement', 'sponsor'])) {
            return $this->core->setResponse(
                'error',
                'Type only placement or sponsor.',
                NULL,
                FALSE,
                400
            );
        }

        // Get member based on uuid
        $member = Member::where('uuid', $uuid)->first();

        $genealogy = collect();
        if (!$member) {
            return $this->core->setResponse(
                'error',
                'Member not exist.',
                NULL,
                FALSE,
                400
            );
        }

        $member->level = 0;

        $genealogy = $genealogy->concat(
            $this->getGenealogyRecursive($member, $type, 0)
        );

        return $genealogy;
    }

    private function getGenealogyRecursive($member, $type, $level)
    {
        $result = collect([$member]);

        if ($type == 'placement') {
            $up_id = 'placement_id';
        } else if ($type == 'sponsor') {
            $up_id = 'sponsor_id';
        }

        $children = Member::where($up_id, $member->id)->get();

        foreach ($children as $child) {
            $child->level = $level + 1;
            $result = $result->concat(
                $this->getGenealogyRecursive($child, $type, $level + 1)
            );
        }

        return $result;
    }
}
