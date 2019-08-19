<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Location;
use App\Pi;

class PiController extends Controller
{
    public function update(Request $request)
    {

        $poly=  $request->all();
        $pi = "";

        if($pi == ""){
            $pi = Pi::where('pi_id', $poly['ID'])->first();
            $no = 10000+ $pi->id;
        }
        if($pi != "") {
            $arr["pi_id"]  = $poly['ID'];
            $arr["no"]  = $no;
            $arr["lat"] = $poly['Latitude'];
            $arr["lng"] = $poly['Longitude'];
            $arr["color"] = "#000DFF";
            Location::create($arr);
        }

        return response()->json(['status'=>'success']);
    }
}

      