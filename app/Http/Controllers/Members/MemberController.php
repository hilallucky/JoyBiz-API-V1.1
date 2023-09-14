<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Services\Members\MemberGenealogyService;
use App\Services\Members\MemberGetUplineService;
use App\Services\Members\MemberListService;
use App\Services\Members\MemberRegisterService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private MemberRegisterService $registerService;
    private MemberGenealogyService $genealogyService;
    private MemberGetUplineService $getUplineService;
    private MemberListService $memberListService;

    public function __construct(
        MemberRegisterService $registerService,
        MemberGenealogyService $genealogyService,
        MemberListService $memberListService,
        MemberGetUplineService $getUplineService,
    ) {
        $this->registerService = $registerService;
        $this->genealogyService = $genealogyService;
        $this->memberListService = $memberListService;
        $this->getUplineService = $getUplineService;
    }

    public function register(Request $request)
    {
        return $this->registerService->register($request);
    }

    public function getGenealogy($uuid, $type)
    {
        return $this->genealogyService->getGenealogy($uuid, $type);
    }

    public function getUpline($uuid, $type)
    {
        return $this->getUplineService->getUpline($uuid, $type);
    }

    public function getMemberList(Request $request)
    {
        return $this->memberListService->getMemberList($request);
    }

    public function checkNetwork(Request $request)
    {
        return $this->getUplineService->checkNetwork($request);
    }
}
