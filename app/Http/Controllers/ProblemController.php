<?php

namespace App\Http\Controllers;

use App\Models\Problem;

class ProblemController extends Controller
{
    public function index()
    {
        $problems = Problem::all();
        return view('problems.index', compact('problems'));
    }

    public function show(Problem $problem)
    {
        $problem->load('slots');
        return view('problems.show', compact('problem'));
    }
}
