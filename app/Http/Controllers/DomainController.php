<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain;
class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domain)
    {
        $data=domain($domain);
       return $data['id'];
    }

    
}
