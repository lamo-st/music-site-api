<?php

namespace App\Http\Controllers\API;

use App\Models\Song;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{
    use APIResponse;

    public function getAllSongs()
    {
        $data['songs'] = Song::join('artists', 'songs.artist_id', '=', 'artists.id')
                            ->select('songs.*', 'artists.image' ,DB::raw("CONCAT(artists.f_name, ' ', IFNULL(artists.l_name, '')) as artist_name"))
                            ->orderBy('songs.created_at', 'desc')
                            ->get();

        return $this->createAPIResponse(true, $data);
    }

    //-----------------------------------
    public function getSong($id)
    {
        $data['song'] = Song::find($id);

        return $this->createAPIResponse(true, $data);
    }

    //--------------------------------------
    public function addSong(Request $request)
    {
        $rules = [
            'title' => 'required',
            'type' => 'required',
            'price' => 'required|numeric|gt:0',
            'artist_id'  => 'required',
        ];

        $messages = [
            'title.required' => 'Title is required',
            'type.required' =>  'Type is required',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price should be a number',
            'price.gt' => 'Price should be a positve value',
            'artist_id.required' => 'Choose a singer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->createAPIResponse(false, null, null, $validator->errors());
        }

        Song::create([
            'title' => $request->title,
            'type' => $request->type,
            'price' => $request->price,
            'artist_id' => $request->artist_id
        ]);

        return $this->createAPIResponse(true, null, 'The song was added successfully');
    }

    //--------------------------------------
    public function searchSong(Request $request)
    {
        $term = $request->term;

        $data['songs'] = Song::join('artists', 'songs.artist_id', '=', 'artists.id')
                                ->select('songs.*', 'artists.image' ,DB::raw("CONCAT(artists.f_name, ' ', IFNULL(artists.l_name, '')) as artist_name"))
                                ->where('songs.title', 'LIKE', '%' . $term . '%')
                                ->orderBy('songs.created_at', 'desc')
                                ->get();

        return $this->createAPIResponse(true, $data);
    }
}
