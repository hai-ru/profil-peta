<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;
use Carbon\Carbon;

use DOMDocument;
use Crypt;
use Exception;
use DB;
use Log;

class APIController extends Controller
{
    public function push_linkaja(Request $request)
    {

		$data = (Object) [
			'terminalId'=>'testing_tcash_wco',
			'userKey'=>'wcotest1090',
			'password'=>'@wcotest12',
			'signature'=>'wcotestsign',
			'total'=>$request->total,
			'successUrl'=>'http://serviceump.itkonsultan.id/',
			'failedUrl'=>'http://serviceump.itkonsultan.id/',
		];

    	$param = 'trxId='.str_random(9).
    			 '&terminalId='.$data->terminalId.
    			 '&userKey='.$data->userKey.
    			 '&password='.$data->password.
    			 '&signature='.$data->signature.
    			 '&total='.$data->total.
    			 '&successUrl='.$data->successUrl.
    			 '&failedUrl='.$data->failedUrl;
    			 '&items='.rawurlencode('[["Deposit","'.$data->total.'","1"]]');

		$command = 'curl -d "'.$param.'" -H "Content-Type: application/x-www-form-urlencoded" -X POST  https://payment.linkaja.id/linkaja-api/api/payment';
		$response = exec($command);
		$j = json_decode($response);
		$db_sementara = file_get_contents(public_path('/db_sementara.txt'));
		$database = json_decode($db_sementara,true);
		$database[] = $j;
		$save_data = json_encode($database);
		file_put_contents(public_path('/db_sementara.txt'), $save_data);
		$datas['response'] = $j;
		return view('redirect-page-api',$datas);
    }

    public function push_midtrans(Request $request)
    {
    	$nominal = intval($request->total);
    	// $nominal = 10000;

    	$transaction_details = [
            'order_id' => str_random(8),
            'gross_amount' => $nominal

        ];
        
        $customer_details = [
            'first_name' => 'ServiceUMP',
            'email' => 'ServiceUMP@serviceump.itkonsultan.id',
            'phone' => '082255985321'
        ];
        
        $custom_expiry = [
            'start_time' => date("Y-m-d H:i:s O", time()),
            'unit' => 'hour',
            'duration' => 1
        ];
        
        $item_details = [
            'id' => str_random(2).'-01',
            'quantity' => 1,
            'name' => 'Deposit UMP Wallet',
            'price' => $nominal
        ];

        // Send this options if you use 3Ds in credit card request
        $credit_card_option = [
            'secure' => true, 
            'channel' => 'migs'
        ];

        $transaction_data = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details,
            'expiry' => $custom_expiry,
            'credit_card' => $credit_card_option,
        ];

        $token = rawurlencode( json_encode($transaction_data) );

        $client = new Client([
		  'base_uri' => 'http://event.itkonsultan.id/',
		]);

		$response = $client->post('api/midtrans/'.$token);
		$body = $response->getBody();
	    return redirect()->to($body);
    }

    public function pricing()
    {
		$db_sementara = file_get_contents(public_path('/db_harga_midtrans.txt'));
		$data['midtrans'] = json_decode($db_sementara,true)[0];

		$content = file_get_contents(public_path('/db_harga_doku.txt'));
		$db_sementara = json_decode($content,true);
		$data['doku'] = array_slice($db_sementara[0], 0, 6, true);
		$data['doku_wallet'] = array_slice($db_sementara[0], 6, 15, true);
		return view('harga',$data);
    }

    public function tarek_data()
    {
        /*
        Tarek Data Dflash
        */

        $file = file_get_contents('https://dflash.co.id/harga/pricelist.php?title=yes&id=9b829ba6882babb164f5f843dcacdb1fffe23e0d7e4d32dfbd2a92519752755b8741a798d39fe2e4a820e5d944a8d65f-2');
        $table = explode('<table class="tabel">', $file);
        unset($table[0]);
        foreach ($table as $key => $value) {
            $rows = explode('<tr class="td', $value);
            unset($rows[0]);
            foreach ($rows as $i => $row) {
                $cell = explode('<td>', $row);
                unset($cell[0]);
                if (array_key_exists(3, $cell)) {
                    $price = intval( preg_replace('/\D/', '', strip_tags($cell[3]) ) );
                    $percent = ($price*4)/100;
                    $price_user = $price+$percent;
                    $data = ['code'=>strip_tags($cell[1]),'name'=>strip_tags($cell[2]),'price'=>$price,'price_user'=>$price_user,'status'=>str_replace(' ', '', strip_tags($cell[4]))];
                    $p = \App\product::create($data);
                }
            }
        }
        return 'success';
    }

    public function documentation()
    {
        return view('v1.developer.documentation');
    }

    public function category(Request $request)
    {
        $r = ServiceController::check_token($request->token);
        if($r['status'] == false){
            return json_encode($r);
        } else {
            $data = \App\category::where("status",1)
            ->get();
            return json_encode( ['status'=>true,'message'=>$data] );
        }
    }

    public function subcategory(Request $request,$id)
    {
        $r = ServiceController::check_token($request->token);
        if($r['status'] == false){
            return json_encode($r);
        } else {
            $data = \App\subcategory::where([
                'cat_id'=>$id,
                'status'=>1
            ])->get();
            return json_encode(['status'=>true,'message'=>$data] );
        }
    }

    public function product(Request $request,$id)
    {
        $r = ServiceController::check_token($request->token);
        if($r['status'] == false){
            return json_encode($r);
        } else {
            $p = \App\product::where('subcat_id',$id)
                    ->with('subcategory.category')
                    ->where('status','open')
                    ->get();
            return json_encode( ['status'=>true,'message'=>$p] );
        }
    }

    public function create_wallet(Request $request)
    {
        try {
            $data = $request->all();
            $u = \App\pengguna::where('username',$data['username'])
            ->where("nohp",$data['nohp'])
            ->first();
            if (!empty($u)) {
                return json_encode(['status'=>false,'message'=>'username sudah terdaftar',"code"=>0]);
            }
            // $u = \App\pengguna::where("nohp",$data['nohp'])
            // ->first();
            // if (!empty($u)) {
            //     return json_encode(['status'=>false,'message'=>'nohp sudah terdaftar',"code"=>0]);
            // }
            $data['password'] = Crypt::encryptString($data['password']);
            $p = \App\pengguna::buat($data);
            $w = $p->wallet()->create(['amount'=>Crypt::encryptString(0)]);
            return json_encode(['status'=>true,'message'=>'wallet is success created',"code"=>1]);
        } catch (Exception $e) {
            Log::error("create_wallet => ".$e->getMessage());
            return json_encode(['status'=>false,'message'=>'something wrong',"code"=>-1]);
        }
    }

    public function deposit(Request $request)
    {
        $log = [
                'activity'=>'deposit',
                'ip'=>$request->ip(),
                'params'=>json_encode($request->all()),
        ];
        try {
            $data = $request->all();
            $r = ServiceController::check_token($data['token']);
            if ($r['status']) {
                $minimum = \App\config::first()->minimum_deposit;
                if ( $data['nominal'] < intval($minimum) ) {
                    return ['status'=>false,'message'=>'Minimum deposit sebesar Rp. '.number_format($minimum,0,',','.')];
                }

                $data['refid'] = strtoupper( str_random(5) );
                $data['jenis'] = 'TOPUP SALDO';
                $data['tipe'] = 'MIDTRANS';
                $data['name'] = $r['data_user']->name;
                $data['nohp'] = $r['data_user']->nohp;
                $data['slug_user'] = $r['data_user']->slug;
                $m = MidtransController::checkout($data);
                if (!$m['status']) {
                	return $m;
                }

                $data['amount'] = $r['data_user']->wallet->amount;
                $data['snap_token'] = $m['token'];

                $t = \App\transaction::create($data);

                $midtrans = [
                    'refid'=>$data['refid'],
                    'snap_token'=>$data['snap_token']
                ];

                $create_midtrans = $t->midtrans()->create($midtrans);

                $result = json_encode(['status'=>true,'message'=>$m['message']]);
                $log['apikey']=$r['data_token']->apikey;
            } else {
                $result = json_encode($r);
            }
            $log['message']=$result;
            \App\log_service::create($log);
            return $result;
        } catch (Exception $e) {
            $result = json_encode(['status'=>true,'message'=>'something error','debug'=>$e->getMessage()]);
            $log['message']=$result;
            \App\log_service::create($log);
            return $result;
        }
    }

    public function check_wallet(Request $request)
    {
        try{
            $data = $request->all();
            $r = ServiceController::check_token($data['token']);
            if ($r['status']) {
                // Log::info("user check wallet => ".json_encode($r["data_user"]));
                $wallet = $r['data_user']->wallet;
                $amount = Crypt::decryptString($wallet->amount);
                if($request->has("fcm")){
                    $r["data_user"]->update(["fcm_token"=>$request->fcm]);
                }
                return json_encode(['status'=>true,'message'=>[
                    'saldo'=>$amount,
                    "pemasukan"=>"0",
                    "pengeluaran"=>"0"
                ]]);
            } else {
                return json_encode($r);
            }
        } catch(Exception $e){
            Log::info("e => ".$e->getMessage());
            return json_encode(['status'=>false,'message'=>'Oops Something error.']);
        }
    }

    public function add_bank_account(Request $request)
    {
        try{
            $data = $request->all();
            $r = ServiceController::check_token($data['token']);
            if ($r['status']) {
                $bank = $r['data_user']->bank()->create($data);
                return json_encode(['status'=>true,'message'=>$r['data_user']->bank]);
            } else {
                return json_encode($r);
            }
        } catch(Exception $e){
            return json_encode(['status'=>false,'message'=>'Oops Something error.']);
        }
    }

    public function bank(Request $request)
    {
        try{
            $data = $request->all();
            $r = ServiceController::check_token($data['token']);
            if ($r['status']) {
                return ['status'=>true,'message'=>\App\bank_corporate::orderBy('bank_name','desc')->get()];
            } else {
                return json_encode($r);
            }
        } catch(Exception $e){
            return ['status'=>false,'message'=>$e->getMessage()];
        }
    }

    public function bank_list(Request $request)
    {
        try{
            $data = $request->all();
            $r = ServiceController::check_token($data['token']);
            if ($r['status']) {
                return ['status'=>true,'message'=>$r['data_user']->bank];
            } else {
                return json_encode($r);
            }
        } catch(Exception $e){
            return ['status'=>false,'message'=>$e->getMessage()];
        }
    }

    public function all_payment_success()
    {
    	try{
	    	$t = \App\transaction::where('jenis','TOPUP SALDO')->where('status',0)->get();
	    	foreach ($t as $key => $value) {
	    		if ($value->status != 20) {
			    	$value->setSukses();
			    	$p = $value->pengguna->wallet;
			    	if (!empty($p)) {
				    	$amount = intval( Crypt::decryptString($p->amount) );
				    	$amount = $amount + intval($value->nominal);
				    	$p->amount = Crypt::encryptString($amount);
				    	$p->save();
			    	}
		    	}
	    	}
	    	return ['status'=>true,'message'=>'success'];
    	} catch(Exception $e){
    		return ['status'=>false,'message'=>$e->getMessage()];
    	}
    }

    public function test_notif()
    {
    	
        $r = DB::connection('android')->table('login_app')->where('username','rektor')->first();
        // DD($r);
    	// $response = [];
    	// foreach ($t as $key => $value) {
    		$data['token'] = $r->fcm_token;
    		$data['title'] = 'Testing';
    		$data['message'] = 'Hello world';
    		$data['payload'] = [
                'TRANSAKSI' => 'PEMBELIAN',
                'REFID' => 'XWFDDNY1'
            ];
    		$n = NotificationController::send_notif($data);
    		// $response[] = $n;
    	// }
    	return json_encode($n);
    }

    public function test()
    {
        // $input = [
        //     'name' => 'Dashboard MYUMPTK',
        //     'email' => 'dashboard@myumptk.com',
        //     'no_hp' => '085250065033',
        //     'password' => 'Myumptk2019',
        //     'slug' => str_random(16),
        //     'amount' => Crypt::encryptString(0),
        // ];
        // $u = \App\User::create($input);
        // $u->roles()->attach(1);

        // return $u;
        // return \App\servicekey::create(['slug_user'=>'jfqhmGX8JbWOhR6V','apikey'=>str_random(16)]);
    }

    public function cek_transaksi(Request $request)
    {
        // try{
            $data = $request->all();
            // DD($data);
            $r = ServiceController::check_token($data['token']);
            if ($r['status']) {
                $t = $r['data_user']->transaction()
                    ->where('refid',$request->refid)
                    ->where('jenis',$request->tipe)
                    ->first();
                // DD($request->refid, $t);
                if (empty($t)) {
                    return ['status'=>false,'message'=>'REFID tidak ditemukan'];
                }
                // $t = $t->with('dflash');
                // DD($t);
                $tgl = Carbon::parse($t->created_at)->format('d/m/Y H:i:s');
                $tgl_human = Carbon::parse($t->created_at)->diffForHumans();
                // $detail = $t;
                // if (isset($t->dflash->sn)) {
                //     $detail = $t->dflash->sn;
                // }
                // $message = ['refid'=>$t->refid,'status'=>$t->status_text,'tgl'=>$tgl,'detail'=>$detail];
                $amount = intval( Crypt::decryptString($t->amount) );
                $message = [
                        'refid'=>$t->refid,
                        'status'=>$t->status_text,
                        'tgl'=>$tgl,
                        'tgl_human'=>$tgl_human,
                        'jenis'=>$t->jenis,
                        'nominal'=>$t->nominal,
                        'nominal_text'=>number_format($t->nominal,0,',','.'),
                        'biaya'=>$t->biaya,
                        'biaya_text'=>number_format($t->biaya,0,',','.'),
                        'saldo_saat_transaksi'=> $amount,
                        'saldo_saat_transaksi_text'=>number_format($amount,0,',','.'),

                ];

                if ($t->jenis == 'PENARIKAN') {
                    $message['bank'] = $t->bank->bank_name;
                    $message['nomor_rekening']= $t->bank->bank_account;
                    $message['atas_nama']= $t->bank->bank_placeholder;
                }
                if ($t->jenis == 'TERIMA') {
                    $message['pengirim'] = $t->pengirim_data->name;
                    $message['nik_pengirim'] = $t->pengirim_data->username;
                }
                if ($t->jenis == 'KIRIM') {
                    $message['penerima'] = $t->penerima_data->name;
                    $message['nik_penerima'] = $t->penerima_data->username;
                }
                if ($t->jenis == 'TOPUP SALDO') {
                    $message['detail_midtrans'] = json_decode($t->midtrans->callback);
                    $message['saldo_masuk'] = $t->nominal - $t->biaya;
                    $message['saldo_masuk_text'] = number_format($message['saldo_masuk'],0,',','.');
                }
                if ($t->jenis == 'PEMBELIAN') {
                    $keterangan = explode('. Sal:',$t->dflash->message);
                    // DD($keterangan,$t->dflash->message);
                    $message['keterangan'] = $keterangan[0];
                    $message['detail_dflash'] = $t->dflash;
                }
                return ['status'=>true,'message'=>'OK','data'=>$message,'transaction'=>$t];
            } else {
                return json_encode($r);
            }
        // } catch(Exception $e){
            // return ['status'=>false,'message'=>$e->getMessage()];
        // }
    }

    public function cek_hari_libur(Request $request)
    {
        try {
            $h = \App\holiday_date::where('tanggal',$request->tanggal)->first();
            if (empty($h)) {
                return ['status'=>false,'message'=>'data not found','data'=>$h];
            }
            return ['status'=>true,'message'=>'LIBUR','data'=>$h];
        } catch (Exception $e) {
            return ['status'=>false,'message'=>$e->getMessage()];
        }
    }

    public function data_hari_libur(Request $request)
    {
        try {
            $h = \App\holiday_date::whereBetween('tanggal', [$request->start, $request->end])->get();
            if (empty($h)) {
                return ['status'=>false,'message'=>'data not found','data'=>$h];
            }
            return ['status'=>true,'message'=>'OK','data'=>$h];
        } catch (Exception $e) {
            return ['status'=>false,'message'=>$e->getMessage()];
        }
    }

    public function biaya_deposit(Request $request)
    {
        $biaya = \App\config::first()->biaya_deposit;
        return 'Anda akan dikenakan biaya deposit sebesar Rp. '.number_format($biaya,0,',','.').' . Anda yakin ?';
    }

    public function transaction(Request $request)
    {
        try{
    		$data = $request->all();
	    	$s = ServiceController::check_token($data['token']);
	    	if ($s['status'] == false) {
	    		return json_encode($s);
	    	}
	    	$p = \App\product::where('code',$data['code_product'])->first();
	    	if (empty($p)) {
	    		return json_encode(['status'=>false,'message'=>'product not found']);
	    	}
	    	$data['nominal'] = intval($p->price_user);
	    	$wallet = $s['data_user']->wallet;
	    	$amount = intval(Crypt::decryptString($wallet->amount));
	    	if ($amount < intval($data['nominal'])) {
	    		return json_encode(['status'=>false,'message'=>'Saldo tidak cukup.']);
	    	}
	    	$refid = strtoupper( str_random(8) );
	    	$transaction = [
	    		'nominal'=>$data['nominal'],
		        'amount'=>$wallet->amount,
		        'jenis'=>'PEMBELIAN',
		        'tipe'=>'DFLASH',
		        'refid'=> $refid,
		        'code_product'=>$data['code_product'],
		        'status'=>0,
		        'status_text'=>'PROSES',
	    	];
	    	$dflash = [
		        'code_product'=>$data['code_product'],
		        'destination'=>$data['destination'],
		        'refid'=>$refid,
	    	];
	    	$t = $s['data_user']->transaction()->create($transaction);
            $dg = new DigiFlazzController;
	    	$r = $dg->topup([
                'uid'=>$refid,
                'sku_code'=>$data['code_product'],
                'customer_no'=>$data['destination'],
            ]);

            $log_dflash = $t->dflash()->create([
                'reponse'=>json_encode($r),
                'tgl_status'=>date("Y-m-d H:i:s"),
                'status'=>$r["rc"],
                'status_text'=>$r["status"],
                'sn'=>$r["sn"],
                'keterangan'=>$r["message"],
                'message'=>$r["message"],
                'harga'=>$r["price"],
                'saldo'=>$r["buyer_last_saldo"],
                'refid'=>$refid,
            ]);

            if(!$r) return ["status"=>false,"message"=>"Provider sedang maintenance"];

            switch ($r["rc"]) {
                case '00':
                    $status = "SUKSES";
                    break;
                case '03':
                    $status = "PROSES";
                    break;
                case '99':
                    $status = "PROSES";
                    break;
                
                default:
                    $status = "GAGAL";
                    break;
            }

            $t->status = $r["rc"];
            $t->status_text = $status;
            $t->save();

            $nominal = $t->nominal;

            if ($amount < $nominal) {
                return ['status'=>false,'message'=>'Saldo tidak cukup.'];
            }
            if($status !== "GAGAL"){
                $t->pengguna->wallet->kurangi($nominal);
                $t->update(['nominal'=>$nominal]);
            }
            return ["status"=>true,"message"=>$r["message"]];
        }catch(\Exception $e){
            return ["status"=>false,"message"=>$e->getMessage()];
        }
    }

}
