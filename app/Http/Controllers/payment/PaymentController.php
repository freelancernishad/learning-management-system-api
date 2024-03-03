<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    function create(Request $request) {

        $baseUrl = 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/';
        $username = '01760418424';
        $Password = 'l0)U%UEh3:Z';
        $app_key = 'YArVqU8ipYJ6jX4QBOTvPjq8tc';
        $app_secret = 'Uh6O8jq2P29sQgEHyGlczm0tY6v1UexXP7h3OJvvZbXiLiiJCvgB';


        $tokenHeader = [
            'username: '.$username,
            'Password: '.$Password,
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        $tokenBody ='{
            "app_key": '.$app_key.',
            "app_secret": '.$app_secret.'
        }';
         $tokenUrl = $baseUrl.'checkout/token/grant';
       $token =  $this->apicall($tokenHeader,$tokenBody,$tokenUrl);

        $id_token = $token->id_token;
       $refresh_token = $token->refresh_token;




        $paymentCreateHeader = [
            'Authorization: '.$id_token,
            'X-App-Key: '.$app_key,
            'Content-Type: application/json'
        ];

        $invoiceNo = 'Inv-'.time();
        $amount = $request->amount;
         $paymentCreateBody ='{
            "mode": "0011",
            "payerReference": "01722597565",
            "callbackURL": "https://softwebsys.com",
            "merchantAssociationInfo": "MI05MID54RF09123456One",
            "amount": "'.$amount.'",
            "currency": "BDT",
            "intent": "sale",
            "merchantInvoiceNumber": "'.$invoiceNo.'"
         }';
         $paymentCreateUrl = $baseUrl.'checkout/create';
        $paymentCreate =  $this->apicall($paymentCreateHeader,$paymentCreateBody,$paymentCreateUrl);


      return ['paymentID'=>$paymentCreate->paymentID,'bkashURL'=>$paymentCreate->bkashURL];






    }


    function apicall($header=[],$body='',$url=''){


        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$body,
        CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }


}
