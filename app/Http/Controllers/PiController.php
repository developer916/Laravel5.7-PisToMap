<?php
  
namespace App\Http\Controllers;
  
use App\Pi;
use Illuminate\Http\Request;
use Mapper;
use App\Location;
use Illuminate\Support\Facades\DB;

class PiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pis = Pi::orderBy('pi_id', 'asc')->paginate(20);
  
        return view('pis.index',compact('pis'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pis.create');
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'pi_id' => 'required',
            'name' => 'required',
            'mac_address' => 'required',
        ]);
  
        Pi::create($request->all());
   
        return redirect()->route('pis.index')
                        ->with('success','Pi created successfully.');
    }
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePi(Request $request)
    {
        $input = $request->all();
        $pi_id = $input["pi_id"];
        $description = $input["description"];
        $polys = json_decode($input["polysJSON"]);

        Location::where('pi_id', $pi_id)->delete();
        $arr = array();
        foreach ($polys as $poly) {
            $arr["pi_id"] = $pi_id;
            $arr["no"]  = $poly->no;
            $arr["lat"] = $poly->lat;
            $arr["lng"] = $poly->lng;
            $arr["color"] = $poly->color;

            Location::create($arr);
        }
        Pi::where('pi_id', $pi_id)->update(["description" => $description]);
        return response()->json(['success'=>'Pi locations saved successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pi  $pi
     * @return \Illuminate\Http\Response
     */
    public function show(Pi $pi)
    {
        $pi_id = $pi->pi_id;
        $colors = [];
        $getColors = Location::where('pi_id', $pi_id)->groupBy('no')->get();
        if(count($getColors) >0){
            for($i = 0; $i< count($getColors); $i++){
                $colors[$i] = $getColors[$i]->color;
            }
        }
        $locations = Location::where('pi_id', $pi_id)->get();
        $pi = Pi::where("pi_id", $pi_id)->first();
        $description = $pi->description;

        //$maxno = DB::select("SELECT max(no) as maxno FROM locations WHERE pi_id='$pi_id'");
        //$max_no = $maxno[0]->maxno;

        $max_no = Location::where('pi_id', $pi_id)->max('no');
        if(!$max_no) $max_no = 0;

        $pi_locs = array();    
        foreach ($locations as $location) {
            array_push($pi_locs, ["no" => $location->no, "lat" => $location->lat, "lng" => $location->lng, "color" => $location->color]);
        }

        $pi_locs = json_encode($pi_locs);
        $get_colors = json_encode($colors);

        return view('pis.show', compact('pi_id', 'max_no', 'pi_locs', 'get_colors', 'description'));
    }
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pi  $pi
     * @return \Illuminate\Http\Response
     */
    public function edit(Pi $pi)
    {   
        return view('pis.edit',compact('pi'));
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pi  $pi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pi $pi)
    {
        $request->validate([
            'pi_id' => 'required',
            'name' => 'required',
            'mac_address' => 'required',
        ]);
  
        $pi->update($request->all());
  
        return redirect()->route('pis.index')
                        ->with('success','Pi updated successfully');
    }
  
    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pi  $pi
     * @return \Illuminate\Http\Response
     */
    public function deletePi(Request $request)
    {
        $input = $request->all();
        Location::where('pi_id', '=', $input["pi_id"])->delete();
        Pi::where('pi_id', '=', $input["pi_id"])->update(["description" => ""]);

        return response()->json(['success'=>'Pi locations deleted successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pi  $pi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pi $pi)
    {
        $pi->delete();
  
        return redirect()->route('pis.index')
                        ->with('success','Pi deleted successfully');
    }
}