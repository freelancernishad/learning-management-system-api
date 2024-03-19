<?php

namespace App\Http\Controllers\payment;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Student;

class PaymentController extends Controller
{


    public function index(Request $request)
    {

        $perpage = $request->perpage;
        if($perpage){
            $students = Payment::paginate($perpage);
        }else{
            $students = Payment::all();
        }
        return response()->json($students);
    }





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
            "payerReference": "01700000000",
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
        $payment->course_id = $request->input('course_id');
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


    function queryPayment(Request $request) {

        $paymentID = $request->paymentID;
        $payment = Payment::with(['student','course'])->where('paymentID',$paymentID)->first();

        $id_token = $payment->id_token;
        $app_key = $payment->app_key;







        //  $baseUrl = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/';
        //  $paymentCreateUrl = $baseUrl.'checkout/execute';

        // $paymentCreate =  $this->apicall($paymentCreateHeader,$paymentCreateBody,$paymentCreateUrl);


        $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/payment/status',
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


$res =  json_decode($response);
if($res->transactionStatus=='Completed' && $res->verificationStatus=='Complete'){
  $payment->update(['status'=>'Paid']);

  $enrolldata = [
    'student_id'=>$payment->student_id,
    'course_id'=>$payment->course_id,
  ];
  $checkenrolment = StudentEnrollment::where($enrolldata)->count();
  if($checkenrolment<1){
      $enrollment = StudentEnrollment::create($enrolldata);
  }


}
  $resData = ['bkash'=>$res,'payment'=>$payment];

return $resData;






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



    function ekpayPayment(Request $request) {

        $trnx_id = 'Inv-'.time();
        $Totalamount = $request->amount;
        $callbackURL = $request->callbackURL;
        $student_id = $request->input('student_id');
        $course_id = $request->input('course_id');
        $amount = 0;

        if ($Totalamount == null || $Totalamount == '' || $Totalamount < 1) {
            $amount = 1;
        } else {
            $amount = $Totalamount;
        }

        $cust_info = [
            "cust_email" => "",
            "cust_id" => "$student_id",
            "cust_mail_addr" => "Address",
            "cust_mobo_no" => "+8801909756552",
            "cust_name" => "Customer Name"
        ];
        $trns_info = [
            "ord_det" => 'sonod',
            "ord_id" => "$course_id",
            "trnx_amt" => $amount,
            "trnx_currency" => "BDT",
            "trnx_id" => "$trnx_id"
        ];
        // return $sonod->unioun_name;


            $redirectutl = ekpayToken($trnx_id, $trns_info, $cust_info,$callbackURL);



        $req_timestamp = date('Y-m-d H:i:s');

        $payment = new Payment();
        $payment->student_id = $request->input('student_id');
        $payment->course_id = $request->input('course_id');
        $payment->trxid = $trnx_id;
        $payment->amount = $amount;
        $payment->total_amount = $amount;
        $payment->mobile_no = '+8801909756552';
        $payment->date = date('Y-m-d');
        $payment->status = 'Pending';
        $payment->month = date('F');
        $payment->year = date('Y');
        $payment->payment_type = 'ekpay';
        $payment->payment_url = $redirectutl;
        $payment->save();

        return $redirectutl;



    }


    function ekpayPaymentIpn(Request $request) {
        $data = $request->all();
        Log::info(json_encode($data));
         $trnx_id = $data['trnx_info']['mer_trnx_id'];
        $payment = payment::where('trxid', $trnx_id)->first();

        $Insertdata = [];
        if ($data['msg_code'] == '1020') {
            $Insertdata = [
                'status' => 'Paid',
                'method' => $data['pi_det_info']['pi_name'],
            ];

            $enrolldata = [
                'student_id'=>$payment->student_id,
                'course_id'=>$payment->course_id,
              ];

              $PayStudent = Student::find($payment->student_id);
              $referedStudent = Student::find($PayStudent->referedby);



              $checkenrolment = StudentEnrollment::where($enrolldata)->count();
              if($checkenrolment<1){
                  $update = $referedStudent->update(['balance'=>$referedStudent->balance+500]);
                  $enrollment = StudentEnrollment::create($enrolldata);
              }

        } else {
            $Insertdata = ['status' => 'Failed',];
        }
        $Insertdata['ipnResponse'] = json_encode($data);
        // return $Insertdata;
        return $payment->update($Insertdata);
    }


    public function ekpayReCallIpn(Request $request)
    {

        $trnx_id = $request->trnx_id;

        $payment = payment::where('trxid', $trnx_id)->first();

        $trans_date = date("Y-m-d", strtotime($payment->date));

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => env('AKPAY_API_URL').'/get-status',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{

         "trnx_id":"'.$trnx_id.'",
         "trans_date":"'.$trans_date.'"

        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response1 = curl_exec($curl);

        curl_close($curl);
         $data =  json_decode($response1);



        //  return $data;

        if($payment->status=='Paid'){

            $message =  "This Transition already paid";
            return [
                'message'=>$message,
                'ipn'=>$data,
            ];
        }


        $Insertdata = [];
        if ($data->msg_code == '1020') {
            $Insertdata = [
                'status' => 'Paid',
                'method' => $data->pi_det_info->pi_name,
            ];
            $enrolldata = [
                'student_id'=>$payment->student_id,
                'course_id'=>$payment->course_id,
              ];
              $checkenrolment = StudentEnrollment::where($enrolldata)->count();
              if($checkenrolment<1){
                  $enrollment = StudentEnrollment::create($enrolldata);
              }
        } else {
            $Insertdata = ['status' => 'Failed',];
        }
        $Insertdata['ipnResponse'] = json_encode($data);
        // return $Insertdata;
        return $payment->update($Insertdata);



    }


}
