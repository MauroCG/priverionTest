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
            'id' => $request->input('id')
        ]);
        $pet->name = $request->input('name');

        if ($request->has('photo')) {
            $photo = $request->file('photo');
            $extension = $photo->extension();

            // Required for make each photo file name different
            if ($request->has('id')) {
                $count = $request->input('id');
            } else {
                $count = Pet::all()->count();
                $count += 1;
            }

            $photoFilename = 
                "$count-$pet->name.$extension";
            $pet->photo_filename = $photoFilename;
            Storage::putFileAs('pets/', $photo, $photoFilename);
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

        foreach ($pets as $pet) { // Simulating file server url -using node http-serverin the pets folder toserve files
            if ($pet->photo_filename) {
                $pet->photo = "http://127.0.0.1:8080/$pet->photo_filename";
            }
        }

        return response()->json([
            'success' => true,
            'pets' => $pets
        ]);
    }

    /**
     * Mehtod ussed to get a pet image
     */
    public function getImage ($photo_filename)
    {
        //return response()->file();
    }

    /**
     * Method used to delete one registry
     * 
     * @param int $id
     */
    public function delete ($id)
    {
        $pet = Pet::where(
            'id', $id
        )->first();

        Storage::delete("pets/$pet->photo_filename");
        $pet->delete();

        return response()->json([
            'success' => true,
            'message' => 'The registry was deleted successfully'
        ]);
    }
}
