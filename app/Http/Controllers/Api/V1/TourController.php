<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourListRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;




class TourController extends Controller
{
    public function index(Travel $travel,TourListRequest $request){
       
        $tours= $travel->tours()
        ->when($request->pricefrom,function($query) use ($request)
        {
            $query->where('price','>=',$request->pricefrom*100) ;   //because the getter of the price is /100 so we have to make *100 to reach the value in db

        })
        ->when($request->priceto,function($query) use ($request)
        {
            $query->where('price','<=',$request->priceto*100) ;  
        })
        ->when($request->datefrom,function($query) use ($request)
        {
            $query->where('starting_date','>=',$request->datefrom) ;  
        })
        ->when($request->dateto,function($query) use ($request) //in the documenation want us to make the datefrom and dateto related to starting date
        {
            $query->where('starting_date','<=',$request->dateto) ;  
        })
        ->when($request->sortby&& $request->sortorder,function($query) use ($request) //in the documenation want us to make the datefrom and dateto related to starting date
        {
            $query->orderBy($request->sortby,$request->sortorder) ;  
        })
        
        ->orderBy('starting_date')
        ->paginate();

        return TourResource::collection($tours);

    }
}
