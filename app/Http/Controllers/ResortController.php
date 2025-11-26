<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class ResortController extends Controller
{
	public function index()
	{
		$resorts = Resort::all();

		return view('resorts', ['resorts' => $resorts]);
	}
}
