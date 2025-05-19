<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NullToBlank
{
    /**
     * Takes the data being requested and sets NULL values to Blanks instead
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Convert response to array and replace NULL values with blanks
        if($response instanceof JsonResponse) {
            $data = $response->getData(true);  // Convert JSON Response to Array

            // Call private function to convert NULL values to blanks
            $data = $this->replaceNULL($data);

            // Update the response
            $response->setData($data);
        }

        return $response;
    }


    private function replaceNULL($data) {
        return collect($data)->map(function ($item) {
            if( is_array($item) || is_object($item) ) {
                return $this->replaceNULL((array) $item);
            }
            return $item === null ? '' : $item;
        })->toArray();
    }
}
