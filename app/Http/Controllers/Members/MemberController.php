<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Members\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            //  'email' => 'required|email|unique:members',
            // 'country_id' => 'required',
            'sponsor_id' => 'required',
            'phone' => 'required|numeric',
            'status' => 'required|numeric',
        ]);

        $member = new Member;
        $member->first_name = $request->first_name;
        $member->last_name = $request->last_name;
        $member->sponsor_id = $request->sponsor_id;
        $member->phone = $request->last_name;
        $member->status = $request->status;
        $member->save();

        // return response()->json([
        //     'status' => 'Success',
        //     'message' => 'Member registered successfully',
        //     'data' => $member
        // ], 201);
        return $this->core->setResponse(
            'success',
            'Member registered successfully',
            $member
        );
    }
}
