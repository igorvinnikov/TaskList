<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Class IndexController
 *
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * @return View
     */
    public function show() : View
    {
        return view('app.taskList');
    }
}
