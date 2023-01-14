<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    /**
     * Method used to create or update the info of a pet
     * 
     * @param Request $request
     * 
     * @return Response json
     */
    public function newOrUpdate (Request $request)
    {
        // Validation messages to reutrn in case of a rule fails
        $validationMessages = [
            'required' => 'The field :attribute is required'
        ];

        $request->validate([
            'name' => 'required'
        ], $validationMessages);


        $pet = Pet::firstOrNew([
            'id' => $request('id')
        ])->first();
        $pet->name = $request->input('name');

        if ($request->has('photo')) {
            $photo = $request->file('photo');
            $extension = $photo->extension();
            $count = Pet::all()->count();
            $count += 1;    // Required for make each photo file name different
            $photoFilename = 
                "$count-$request->input('name').$extension";
            $pet->photo_filename = $photoFilename;
            Storage::putFileAs('', $photo, $photoFilename);
        }

        $pet->save();

        return response()->json([
            'success' => true,
            'message' => 'Registry created/updated successfully'
        ]);
    }

    /**
     * Method used to get one or all the registries
     * 
     * @param int $id (optional):
     * When given one registry is fetched from the DB
     */
    public function get ($id = null)
    {
        if ($id) {
            $pets = Pet::where(
                'id', $id
            )->first();
        } else {
            $pets = Pet::all();
        }

        return response()->json([
            'success' => true,
            'pets' => $pets
        ]);
    }

    /**
     * Method used to delete one registry
     * 
     * @param int $id
     */
    public function delete ($id)
    {
        Pet::where(
            'id', $id
        )->delete();

        return response()->json([
            'success' => true,
            'message' => 'The registry was deleted successfully'
        ]);
    }
}
