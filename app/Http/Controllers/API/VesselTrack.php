<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VesselTrackModel;
use App\Models\UserRequest;
use Response;

class VesselTrack extends Controller
{
    public function index(Request $request) {
        
        $mmsi = isset($request->mmsi) ? explode(",", $request->mmsi) : [];
        $lat = $request->lat;
        $long = $request->long;
        $start = $request->start;
        $end = $request->end;

        $vesselTracks = VesselTrackModel::query();

        // Single and Multiple MMSI filter
        if(count($mmsi)) {
            $vesselTracks->whereIn('mmsi', $mmsi);
        }

        // Time interval filter, need to be tested  .
        if(!is_null($start) && !is_null($end)) {
            print_r("hi2");
            $vesselTracks->whereBetween('created_at',[strtotime($start), strtotime($end)]);            
        }

        // @TODO: lat and long filters need more clarification, should this be calculated on radius base or distance base?
        
        // Export to csv.
        if ($request->csv) {
            
            $vesselTracks = $vesselTracks->get();

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=vesselTrack.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
        
            $columns = array('id', 'mmsi', 'status', 'station', 'speed', 'lon', 'lat', 'course', 'heading', 'rot', 'created_at', 'updated_at');
        
            $callback = function() use ($vesselTracks, $columns)
            {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
        
                foreach($vesselTracks as $vesselTrack) {
                    fputcsv($file, array($vesselTrack->id, $vesselTrack->mmsi, $vesselTrack->status, $vesselTrack->station, $vesselTrack->speed, $vesselTrack->lon, $vesselTrack->lat, $vesselTrack->course, $vesselTrack->heading, $vesselTrack->rot, $vesselTrack->created_at, $vesselTrack->updated_at));
                }
                fclose($file);
            };
            return Response::stream($callback, 200, $headers);

        }

        return $vesselTracks->paginate(10);
    
    }

    public function upload(Request $request) {

        
        $user = UserRequest::where('user_ip', $_SERVER['REMOTE_ADDR'])->get();

        // If a new user.
        if (count($user) == 0) {
            $user = new UserRequest;
            $user->user_ip = $_SERVER['REMOTE_ADDR'];
            $user->count_last_hour = 1;
            $user->save();
        }
        else {
            // If existing user.
            $user = $user[0];
            $user->count_last_hour += 1;

            // Restart counter in the new hour.
            if(strtotime($user->updated_at) < strtotime("-60 minutes")) {
                $user->count_last_hour = 1;
            }
            
        }

        // Upload file if, file exist and limit not reached.
        if($request->file('file') && $user->count_last_hour < 11){
            $data = json_decode(file_get_contents($request->file('file')), true);
            
            foreach($data[2]['data'] as $row) {

                $newVesselTrack = new VesselTrackModel;
                $newVesselTrack->mmsi = $row['mmsi'];
                $newVesselTrack->status = $row['status'];
                $newVesselTrack->station = $row['station'];
                $newVesselTrack->speed = $row['speed'];
                $newVesselTrack->lon = $row['lon'];
                $newVesselTrack->lat = $row['lat'];
                $newVesselTrack->course = $row['course'];
                $newVesselTrack->heading = $row['heading'];
                $newVesselTrack->rot = $row['rot'];
                $newVesselTrack->save();

            }
            // Increase count of user.
            $user->save();
        }
        else {
            return "Request limit reached!";
        }

        return "Vessel Tracks Uploaded.";
    }
}
