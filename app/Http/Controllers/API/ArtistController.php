<?php

namespace App\Http\Controllers\API;

use App\Models\Artist;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class ArtistController extends Controller
{
    use APIResponse;

    public function getAllArtists()
    {
        $data['artists'] = Artist::orderBy('created_at', 'desc')->with('songs')->get();

        return $this->createAPIResponse(true, $data);
    }

    //-----------------------------------------
    public function getArtist($id)
    {
        $data['artist'] = Artist::find($id);
        $data['songs'] = $data['artist']->songs();

        return $this->createAPIResponse(true, $data);
    }

    //-----------------------------------------
    public function addArtist(Request $request)
    {
        $rules = [
            'f_name' => 'required',
            'country' => 'required',
            'image' => 'required',
        ];

        $messages = [
            'f_name.required' => 'First name is required',
            'country.required' => 'Country is required',
            'image.required' => 'Image is required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->createAPIResponse(false, null, null, $validator->errors());
        }

        // Image
        $filename = "";
        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $location = public_path('assets/artists/'.$filename);
            // resize
            $manager = new ImageManager(new Driver());
            $resizedImage = $manager->read($request->file('image'));
            $resizedImage->resize(300, 300)->save($location);
        }

        Artist::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'gender' => $request->gender,
            'country' => $request->country,
            'image' => $filename,
        ]);

        return $this->createAPIResponse(true, null, 'The Artist was added successfully');
    }

    //------------------------------------------
    public function searchArtist(Request $request)
    {
        $term = $request->term;

        $data['artists'] = Artist::where('f_name', 'LIKE', '%' . $term . '%')
                                ->orWhere('l_name', 'LIKE', '%' . $term . '%')
                                ->orderBy('created_at', 'desc')
                                ->with('songs')
                                ->get();

        return $this->createAPIResponse(true, $data);
    }

    //--------------------------------------
    public function artistsList()
    {
        $data['artists'] = Artist::select('id', DB::raw("CONCAT(artists.f_name, ' ', IFNULL(artists.l_name, '')) as name"))
                                    ->orderBy('name')
                                    ->get();

        return $this->createAPIResponse(true, $data);
    }
}
