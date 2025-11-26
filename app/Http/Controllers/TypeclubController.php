<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Typeclub;

class TypeclubController extends Controller
{
 public function index()
  {
    $typeclubs = Typeclub::all();

    return view('typeclubs', ['typeclubs' => $typeclubs]);
  }
}

