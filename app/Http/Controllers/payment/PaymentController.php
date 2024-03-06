<?php

namespace App\Http\Controllers\payment;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    function create(Request $request) {

        $baseUrl = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/';
        $username = 'sandboxTokenizedUser02';
        $Password = 'sandboxTokenizedUser02@12345';
        $app_key = '4f6o0cjiki2rfm34kfdadl1eqq';
        $app_secret = '2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b';


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
        $callbackURL = $request->callbackURL;
        // "payerReference": "",
         $paymentCreateBody ='{
            "mode": "0011",
        
            "callbackURL": "'.$callbackURL.'",
            "merchantAssociationInfo": "MI05MID54RF09123456One",
            "amount": "'.$amount.'",
            "currency": "BDT",
            "intent": "sale",
            "merchantInvoiceNumber": "'.$invoiceNo.'"
         }';







         $paymentCreateUrl = $baseUrl.'checkout/create';
        $paymentCreate =  $this->apicall($paymentCreateHeader,$paymentCreateBody,$paymentCreateUrl);

        $payment = new Payment();
        $payment->student_id = $request->input('student_id');
        $payment->trxid = $invoiceNo;
        $payment->amount = $amount;
        $payment->total_amount = $amount;
        $payment->mobile_no = '01909756552';
        $payment->date = date('Y-m-d');
        $payment->status = 'Pending';
        $payment->month = date('F');
        $payment->year = date('Y');
        $payment->payment_type = 'online';
        $payment->paymentID = $paymentCreate->paymentID;
        $payment->id_token = $id_token;
        $payment->app_key = $app_key;
        $payment->refresh_token = $refresh_token;

        $payment->payment_url = $paymentCreate->bkashURL;
        $payment->save();


      return ['paymentID'=>$paymentCreate->paymentID,'bkashURL'=>$paymentCreate->bkashURL];

    }


    function checkPayment(Request $request) {

        $paymentID = $request->paymentID;
        $payment = Payment::where('paymentID',$paymentID)->first();

        $id_token = $payment->id_token;
        $app_key = $payment->app_key;







        //  $baseUrl = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/';
        //  $paymentCreateUrl = $baseUrl.'checkout/execute';

        // $paymentCreate =  $this->apicall($paymentCreateHeader,$paymentCreateBody,$paymentCreateUrl);


        $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/execute',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
	"paymentID" : "'.$paymentID.'"
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: '.$id_token,
    'X-App-Key: '.$app_key,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);






        // return $paymentCreate;


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
