<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class HomeController extends Controller
{
    public $providers;
    public $hotels;
    public $bookings;
    public $status;
    public $message;
    public $details;

    public function __construct()
    {
       //providers collection
       $this->providers=collect([
           [
            'id'=>1,
            'name'=>'BestHotels'
           ],
           [
            'id'=>2,
            'name'=>'TopHotels'
           ]
         
       ]);

       //hotels collection
       $this->hotels=collect([
         [
            'id'=>1,
            'name'=>'The Royal Hotel Hull',
            'provider_id'=>1,
            'fare'=>'10$',
            'city'=>'AUH',
            'all_positions'=>14,
            'discount'=>0,
            'rate'=>3,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>2,
             'name'=>'ibis Hull City Centre',
             'provider_id'=>1,
             'fare'=>'12$',
             'discount'=>1,
             'city'=>'ATL',
             'all_positions'=>20,
             'rate'=>1,
             'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>3,
            'name'=>'Mercure Hull Grange Park Hotel',
            'provider_id'=>1,
            'fare'=>'14$',
            'discount'=>'2$',
            'city'=>'BCN',
            'all_positions'=>22,
            'rate'=>5,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>4,
            'name'=>'Mercure Hull Grange Park Hotel',
            'provider_id'=>1,
            'fare'=>'3$',
            'discount'=>'0$',
            'city'=>'BER',
            'all_positions'=>6,
            'rate'=>4,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>5,
            'name'=>'Holiday Inn Express Hull City Centre',
            'provider_id'=>2,
            'fare'=>'8$',
            'discount'=>'0$',
            'city'=>'BER',
            'all_positions'=>100,
            'rate'=>3,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>6,
            'name'=>'Old Grey Mare Inn by Greene King Inns',
            'provider_id'=>2,
            'fare'=>'20$',
            'discount'=>'2$',
            'city'=>'BKK',
            'all_positions'=>23,
            'rate'=>4,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>7,
            'name'=>'Trinity Hotel',
            'provider_id'=>2,
            'fare'=>'22$',
            'discount'=>'1$',
            'city'=>'BSL',
            'all_positions'=>33,
            'rate'=>2,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>8,
            'name'=>'Admiral of the Humber Wetherspoon',
            'provider_id'=>2,
            'fare'=>'26$',
            'discount'=>'3$',
            'city'=>'BCN',
            'all_positions'=>37,
            'rate'=>5,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>9,
            'name'=>'Gilson Hotel',
            'provider_id'=>1,
            'fare'=>'11$',
            'discount'=>'1$',
            'city'=>'BCN',
            'all_positions'=>140,
            'rate'=>2,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],
         [
            'id'=>10,
            'name'=>'The Carlton Hotel',
            'provider_id'=>1,
            'fare'=>'9$',
            'discount'=>'2$',
            'city'=>'AUH',
            'all_positions'=>56,
            'rate'=>4,
            'amenities'=>['Breakfast','dinner','lunch'],
         ],

       ]);

       //bookings collection
       $this->bookings=collect([
          [
            'id'=>1,
            'from_date'=>date('c',strtotime('2-12-2019')),
            'to_date'=>date('c',strtotime('5-12-2019')),
            'adults_number'=>3,
            'available_adults_number'=>4,//still available for this booking
            'hotel_id'=>1,
          ],
          [
            'id'=>2,
            'from_date'=>date('c',strtotime('2-12-2019')),
            'to_date'=>date('c',strtotime('5-12-2019')),
            'adults_number'=>3,
            'available_adults_number'=>4,//still available for this booking
            'hotel_id'=>2,
          ],
          [
            'id'=>3,
            'from_date'=>date('c',strtotime('2-12-2019')),
            'to_date'=>date('c',strtotime('5-12-2019')),
            'adults_number'=>2,
            'available_adults_number'=>4,//still available for this booking
            'hotel_id'=>3,
          ],
          [
            'id'=>4,
            'from_date'=>date('c',strtotime('2-12-2019')),
            'to_date'=>date('c',strtotime('5-12-2019')),
            'adults_number'=>10,
            'available_adults_number'=>4,//still available for this booking
            'hotel_id'=>4,
          ],
          [
            'id'=>5,
            'from_date'=>date('c',strtotime('2-12-2019')),
            'to_date'=>date('c',strtotime('5-12-2019')),
            'adults_number'=>10,
            'available_adults_number'=>4,//still available for this booking
            'hotel_id'=>5,
          ],
          [
            'id'=>6,
            'from_date'=>date('c',strtotime('10-12-2019')),
            'to_date'=>date('c',strtotime('22-12-2019')),
            'adults_number'=>10,
            'available_adults_number'=>4,//still available for this booking
            'hotel_id'=>6,
          ],
       ]);


    }
    
    //ourHotels Api
    public function Ourhotels(Request $request)
    {
        $rules=[
            'date_from'=>'required|date',
            'date_to'=>'required|date',
            'city'=>'required',
            'adults_number'=>'required'
        ];
        $validation_response=$this->validation($request,$rules);

        if(!$validation_response)
        {
            return $this->response();
        }
        //query from_date and to_date
        $get_available_hotels=$this->queryHotels($request);
        
        //query for the city
        $get_available_hotels=$get_available_hotels->where('city',$request->city)
                                                   ->all();

        //rebuild response collection
        $response_collection=collect();
        foreach($get_available_hotels as $get_available_hotel)
        {
            $response_collection->push([
                                        'provider'=>$this->getProviderName($get_available_hotel['provider_id']),
                                        'hotelName'=>$get_available_hotel['name'],
                                        'fare'=>$get_available_hotel['fare'],
                                        'amenities'=>$get_available_hotel['amenities']
                                    ]);
        }

        $this->status=200;
        $this->message='ok';
        $this->details=$response_collection;

        return $this->response();
      
    }

    //BestHotels Api
    public function BestHotels(Request $request)
    {
        $rules=[
            'date_from'=>'required|date',
            'date_to'=>'required|date',
            'city'=>'required',
            'adults_number'=>'required'
           ];
        $validation_response=$this->validation($request,$rules);
   
        if(!$validation_response)
        {
            return $this->response();
        }
        //query from_date and to_date
        $get_available_hotels=$this->queryHotels($request);
        
        //query for the city and provider
        $get_available_hotels=$get_available_hotels->where('city',$request->city)
                                                    ->where('provider_id',1)
                                                    ->all();
        //rebuild response collection
        $response_collection=collect();
        foreach($get_available_hotels as $get_available_hotel)
        {
            $response_collection->push([
                                       
                                        'hotelName'=>$get_available_hotel['name'],
                                        'hotelRate'=>$get_available_hotel['rate'],
                                        'hotelFare'=>$get_available_hotel['fare'],
                                        'roomAmenities'=>$get_available_hotel['amenities']
                                    ]);
        }

        //return response
        $this->status=200;
        $this->message='ok';
        $this->details=$response_collection;

        return $this->response();

    }

    //TopHotels Api
    public function TopHotels(Request $request)
    {
        $rules=[
            'date_from'=>'required|date',
            'date_to'=>'required|date',
            'city'=>'required',
            'adults_number'=>'required'
           ];
        $validation_response=$this->validation($request,$rules);
   
        if(!$validation_response)
        {
            return $this->response();
        }
         //query from_date and to_date
         $get_available_hotels=$this->queryHotels($request);
        
         //query for the city and provider
         $get_available_hotels=$get_available_hotels->where('city',$request->city)
                                                     ->where('provider_id',2)
                                                     ->all();
         //rebuild response collection
         $response_collection=collect();
         foreach($get_available_hotels as $get_available_hotel)
         {
             $response_collection->push([
                                        
                                         'hotelName'=>$get_available_hotel['name'],
                                         'rate'=>$get_available_hotel['rate'],
                                         'price'=>$get_available_hotel['fare'],
                                         'discount'=>$get_available_hotel['discount'],
                                         'amenities'=>$get_available_hotel['amenities']
                                     ]);
         }
 
         //return response
         $this->status=200;
         $this->message='ok';
         $this->details=$response_collection;
 
         return $this->response();
    }

    //query hotels with date_from , date_to,city and number of adults

    public function queryHotels($request)
    {
        //get already booked
        $date_from=date('c',strtotime($request->date_from)); //convert request date to ISO date format
        $date_to=date('c',strtotime($request->date_to)); //convert request date to ISO date format

        $booked_first=$this->bookings->where('from_date','>=',$date_from)
                                     ->where('to_date','<=',$date_to)
                                     ->where('available_adults_number','<',$request->adults_number)
                                     ->all();
        $booked_second=$this->bookings->where('from_date','<=',$date_from)
                                    ->where('to_date','>=',$date_to)
                                    ->where('available_adults_number','<',$request->adults_number)
                                    ->all();
        $booked_final=$this->bookings->where('from_date','<=',$date_from)
                                    ->where('to_date','<=',$date_to)
                                    ->where('available_adults_number','<',$request->adults_number)
                                    ->all();
                                  
        //push not available hotels ids
        $not_available_hotel_ids=[];
        foreach($booked_first as $booking)
        {
            array_push($not_available_hotel_ids,$booking['hotel_id']);
        } 
        foreach($booked_second as $booking)
        {
            array_push($not_available_hotel_ids,$booking['hotel_id']);
        }  
        foreach($booked_final as $booking)
        {
            array_push($not_available_hotel_ids,$booking['hotel_id']);

        }
        //query hotels where not booked
        $available_hotels=$this->hotels->whereNotIn('id',$not_available_hotel_ids)->all();
        //convert available hotels to collection 
        $available_hotels=collect($available_hotels);

        return $available_hotels;
      
    }

    //get hotel by hotel_id
    public function getHotel($hotel_id)
    {
        $hotel=$this->hotels->where('id',$hotel_id)->all();

        return $provider;
    }

    //get provider by provider_id
    public function getProvider($provider_id)
    {
        $provider=$this->providers->where('id',$provider_id)->all();

        return $provider;
    }
     //get provider by provider_id
     public function getProviderName($provider_id)
     {
         $provider=$this->providers->where('id',$provider_id)->all();
         return $provider[0]['name'];
     }

    //get hotels by provider_id
    public function getProviderHotels($provider_id)
    {
        $hotels=$this->hotels->where('provider_id',$provider_id)->all();

        return $hotels;
    }

    //get provider by hotel_id
    public function getHotelProvider($hotel_id)
    {
        $hotel=$this->hotels->where('id',$hotel_id)->all();
        
        $provider=$this->providers->where('id',$hotel[$hotel_id-1]['provider_id'])->all();

        return $provider;
    }
    //validation
    public function validation($request,$rules)
    {
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
          $this->status=400;
          $error_message=[];
          foreach($validator->errors()->all() as $error)
          {
            array_push($error_message,$error);

          }
          $this->message=$error_message;

          return false;

        }
        else{
            return true;
        }
    }
    //response
    public function response()
    {
        return response()->json([
            'status'=>$this->status,
            'message'=>$this->message,
            'details'=>$this->details
        ]);
    }
}
