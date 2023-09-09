<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Services\Members\MemberGenealogyService;
use App\Services\Members\MemberRegisterService;
use App\Services\Members\MemberService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // private MemberService $registerService;

    // public function __construct(MemberService $registerService)
    // {
    //     $this->registerService = $registerService;
    // }

    // public function register(Request $request)
    // {
    //     return $this->registerService->register($request);
    // }

    // public function getGenealogy($uuid, $type)
    // {
    //     return $this->registerService->getGenealogy($uuid, $type);
    // }

    private MemberRegisterService $registerService;
    private MemberGenealogyService $genealogyService;

    public function __construct(
        MemberRegisterService $registerService,
        MemberGenealogyService $genealogyService
    ) {
        $this->registerService = $registerService;
        $this->genealogyService = $genealogyService;
    }

    public function register(Request $request)
    {
        return $this->registerService->register($request);
    }

    public function getGenealogy($uuid, $type)
    {
        return $this->genealogyService->getGenealogy($uuid, $type);
    }
}
