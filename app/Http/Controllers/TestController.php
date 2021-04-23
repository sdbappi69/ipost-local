<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Traits\LogsTrait;
use DB;
use Auth;
class TestController extends Controller
{
    use LogsTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->suborderStatus('2818', '2');
        // $this->suborderStatus('2818', '5');
        // $this->suborderStatus('2818', '7');
        // $this->suborderStatus('2818', '15');
        // $this->suborderStatus('2818', '26');
        $this->suborderStatus('2818', '29');
        // $this->suborderStatus('2818', '30');
        // $this->suborderStatus('2818', '31');
        return 'Ok';
    }
}
