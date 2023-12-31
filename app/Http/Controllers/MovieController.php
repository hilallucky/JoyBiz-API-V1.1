<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{

    /**
     * get all movie paginate
     *
     * @param  Request $request per_page=15&page=1
     * @return void
     */
    public function all(Request $request)
    {

        /* define record per page  */
        $per_page = (int) ($request->per_page ?? 15);

        /* limit per page not greater then 50 */
        if ($per_page > 50) {

            $per_page = 50;
        }

        return $this->core->setResponse('success', 'Get Movies', Movie::paginate($per_page));
    }

    /**
     * show movie by id
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function show($uuid)
    {
        // print_r($id);

        if (!$movie = Movie::where(['uuid' => $uuid])->first()) {

            return $this->core->setResponse('error', 'Movie Not Found', NULL, FALSE, 404);
        }
        // dd($movie);

        return $this->core->setResponse('success', 'Movie Found', $movie);
    }

    /**
     * create movie
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {

        /* validation requirement */
        $validator = $this->validation(
            'create',
            $request
        );

        if ($validator->fails()) {

            return $this->core->setResponse(
                'error',
                $validator->messages()->first(),
                NULL,
                false,
                400
            );
        }

        $movie = Movie::create($request->all());

        return $this->core->setResponse('success', 'Movie Created', $movie);

    }

    /**
     * update movie
     *
     * @param  Request $request
     * @param  string $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {

        /* validation requirement */
        $validator = $this->validation('update', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), NULL, false, 400);
        }

        $movie = Movie::find($id);

        $movie->fill($request->only(['title', 'description', 'genres', 'embed_url',]))->save();

        return $this->core->setResponse('success', 'Movie Updated', $movie);

    }

    /**
     * add viewed
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function viewed($id)
    {

        if (!$movie = Movie::find($id)) {

            return $this->core->setResponse('error', 'Movie Not Found', NULL, FALSE, 404);
        }

        $movie->viewed++;

        $movie->save();

        return $this->core->setResponse('success', 'Movie viewed added', $movie->viewed);

    }

    /**
     * delete movie
     *
     * @param  string $id
     * @return JsonResponse
     */
    public function delete($id)
    {

        if (!$movie = Movie::find($id)) {

            return $this->core->setResponse('error', 'Movie Not Found', NULL, FALSE, 404);
        }

        $movie->delete();

        return $this->core->setResponse('success', 'Movie deleted');

    }


    /**
     * validation requirement
     *
     * @param  string $type
     * @param  request $request
     * @return object
     */
    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'create' || 'update':

                $validator = [
                    'title' => 'max:100|min:2',
                    'description' => 'required|max:140|min:10',
                    'genres' => 'required|json',
                    'embed_url' => 'required|url|unique:movies',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }

}
