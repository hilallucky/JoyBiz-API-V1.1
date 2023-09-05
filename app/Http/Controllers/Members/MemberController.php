<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Services\Members\MemberService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private MemberService $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function register(Request $request)
    {
        return $this->memberService->register($request);
    }
}
