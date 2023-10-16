<?php

namespace App\Helpers\Bonuses;

use app\Libraries\Core;
use App\Models\Bonuses\CouponsAndRewards\Voucher;
use App\Models\Bonuses\PreparedDataJoy;
use App\Models\Bonuses\Ranks\EffectiveRank;
use App\Models\Bonuses\Ranks\ERank;
use App\Models\Bonuses\Ranks\SRank;
use App\Models\Bonuses\Wallets\Wallet;
use App\Models\Calculations\Bonuses\Period;
use App\Models\Members\Member;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Joybiz extends Model
{


	var $TYPE_USER_LIST = ['SC' => 'Special Customer', 'M' => 'Member'];
	var $TYPE_INVEST_LIST = [
		'M' => [
			'Santunan dana kematian' => 15000,
			'Penghasilan JoySyz' => 20000,
			'Website landing page/Apps Android/IOS' => 100000,
			'Starter kit & CD Produk dan kartu anggota' => 79800,
			'Micro SD JoySyz dan buku foundation pack' => 30000,
			'Biaya kirim' => 10000,
			'PPN' => 25200,
		],
		'SC' => [
			'Mobile Apps' => 10000,
			'Brosur dan kartu anggota' => 25500,
			'Biaya kirim' => 10000,
			'PPN' => 4500
		]
	];
	var $LOCATION = 'WIB';
	var $TYPE_USER = 'M';
	var $PRICE = 0;
	var $PRICE_JOYBIZZER = 0;
	var $NAME = '';
	var $REVENUE_PERCENTAGE_MEMBER = 0.25;
	var $PRICE_TO_PRICE_JOYBIZZER = 1.25;
	var $PRICE_JOYBIZZER_TO_PRICE = 0.8;
	var $BV_PRICE = 1000;
	var $PV_PRICE = 1000;
	var $APPV = 0;
	var $JBP = 0;
	var $VJ = 0;
	var $BJ = 0;
	var $PVJ = 0;
	var $PVB = 0;
	var $BVJ = 0;
	var $BVB = 0;
	var $REGISTER_DATE = null;
	var $IS_PACKET_EXPRESS = false;
	var $PV = 0;
	var $BV = 0;
	var $BV_TO_PV = 1;
	var $BV_TO_PV_EXPRESS = 2.67;
	var $BV_TO_RUPIAH = 10000;
	var $RANK = null;
	var $EFFECTIVE_RANK = null;
	var $CAREER_RANK = null;
	var $USERID = null;
	var $DEBUG = false;
	var $EFEKTIF = [];
	var $KARIR = ['CAREER_RANK' => null, 'EFFECTIVE_RANK' => null, 'APPV' => 0, 'APBV' => 0, 'PVJ' => 0, 'BVJ' => 0, 'PVB' => 0, 'BVB' => 0];
	var $TRANSACTION_DETAIL = [];
	var $RANK_LIST = [
		1 => 'Civilian',
		2 => 'Joykeeper',
		3 => 'Joypriser',
		4 => 'Joypreneur',
		5 => 'JoyBizPreneur',
		6 => 'Baron JBpreneur',
		7 => 'Viscount JBpreneur',
		8 => 'Earl JBpreneur',
		9 => 'Marquis JBpreneur',
		10 => 'Duke JBpreneur',
		11 => 'Crown Ambassador',
		12 => 'Royal Crown Ambassador',
	];
	var $RANK_INDEX_LIST = [
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => 7,
		8 => 8,
		9 => 9,
		10 => 10,
		11 => 11,
		12 => 12,
	];

	function posisi($uid)
	{

		if ($uid) {

			$a = new User;

			//Log::info (print_r($a->findtree($uid)));
			$alldownline = $a->findtree($uid)->toArray();
			$a->generate_level($alldownline);

			$xyz = collect([]);

			foreach ($a->user_level as $key => $value) {

				$appv = 0;
				$apbv = 0;

				foreach ($value as $key2 => $value2) {

					if ($uid != $value2) {

						$k = karir::firstOrCreate(['id' => $value2]);

						$u = User::where('id', '=', $value2)->first();

						$detail = posisi::firstOrCreate([
							"id_user_fk" => $uid,
							"id_downline_fk" => $value2
						]);

						$xyz->push($u->id_upline_fk);

						$detail->id_upline_fk = $u->id_upline_fk;
						$detail->lvl = $key - 1;
						$detail->appv = $k['appv'];
						$detail->apbv = $k['apbv'];

						$detail->index = $key2;
						$detail->save();
					}
				}
			}

			if ($xyz) {

				foreach ($xyz->unique()->values()->all() as $key => $value) {

					$c = posisi::select(DB::raw(
						'SUM(appv) as pgpv'
					), DB::raw('SUM(apbv) as pgbv'))
						->where("id_upline_fk", $value)
						->first();
					$v[$value] = $c->toArray();
				}

				$v = [];

				if (!empty($v)) {

					foreach ($v as $key => $value) {

						if ($uid != $key) {

							$c = posisi::where([
								"id_user_fk" => $uid,
								"id_downline_fk" => $key
							])->update($value);
						} else {

							$k = karir::where('id', '=', $key)->update($value);
						}
					}
				}
			}

			$a = new User;

			$allsponsor = $a->findtree($uid, "sponsorby")->toArray();
			$a->generate_level($allsponsor, 1, 1, 1, "sponsorby");
			//dd($a->user_level);

			$xyz = collect([]);

			foreach ($a->user_level as $key => $value) {

				$appv = 0;
				$apbv = 0;

				foreach ($value as $key2 => $value2) {

					if ($uid != $value2) {

						$k = karir::firstOrCreate(["id" => $value2]);

						$u = User::where('id', '=', $value2)->first();

						$detail = generasi::firstOrCreate([
							"id_user_fk" => $uid,
							"id_downline_fk" => $value2
						]);

						$xyz->push($u->id_sponsor_fk);

						$detail->id_sponsor_fk = $u->id_sponsor_fk;
						$detail->lvl = $key - 1;
						$detail->appv = $k['appv'];
						$detail->apbv = $k['apbv'];

						$detail->index = $key2;
						$detail->save();
					}
				}
			}

			if ($xyz) {

				foreach ($xyz->unique()->values()->all() as $key => $value) {

					$c = generasi::select(DB::raw(
						'SUM(appv) as pgpv'
					), DB::raw('SUM(apbv) as pgbv'))
						->where("id_sponsor_fk", $value)
						->first();
					$v[$value] = $c->toArray();
				}

				$v = [];

				if (!empty($v)) {

					foreach ($v as $key => $value) {

						if ($uid != $key) {

							$c = generasi::where([
								"id_user_fk" => $uid,
								"id_downline_fk" => $key
							])->update($value);
						} else {

							//$k = karir::where('id','=', $key)->update($value);

						}
					}
				}
			}
		}

		$calculatepgpv = karir::all();
		foreach ($calculatepgpv as $key => $value) {
			$total = posisi::select(DB::raw(
				'SUM(appv) as pgpv'
			), DB::raw('SUM(apbv) as pgbv'))
				->where("id_user_fk", $value->id)->where('lvl', '!=', 0)
				->first();
			$u =  karir::where("id", $value->id)->first();
			$u->pgpv = $total->pgpv;
			$u->pgbv = $total->pgbv;
			$u->save();
		}

		if ($uid) {
			$x = $a->where("id", $uid);
		} else {
			$x = $a->where("id_upline_fk", null);
		}

		$u = $x->with("downline.downline.downline")->first()->toArray();

		$u['karir'] = karir::firstOrCreate(["id" => $u['id']]);

		foreach ($u['downline'] as $key => $value) {
			$d = karir::where("id", $value['id'])->first();
			$u['downline'][$key]['karir'] = [];
			if (!empty($d)) {
				$u['downline'][$key]['karir'] = $d->toArray();
			}
			foreach ($value['downline'] as $key2 => $value2) {
				$d = karir::where("id", $value2['id'])->first();
				$u['downline'][$key]['downline'][$key2]['karir'] = [];
				if (!empty($d)) {
					$u['downline'][$key]['downline'][$key2]['karir'] = $d->toArray();
				}
			}
		}

		$data['tree'] = $u;

		return $data;
	}




	function aktivasi_member($id)
	{

		$inp = [
			'activated_at' => date("Y-m-d"),
			"status" => 1
		];

		$user = User::where("id", $id)->first();
		$inp['kode'] = $this->urut_register($user->flag, $user->provinsi);
		$user->fill($inp);
		$user->save();

		$rand = Str::random(6);

		$x = [
			"code" => $rand,
			"name" => "VOUCHER PENDAFTARAN",
			"deduction" => 0,
			"is_register" => 1,
			"id_user_fk" => $id,
		];

		$y = voucher::create($x);
	}

	function urut_id($id, $maxdigit)
	{

		//echo $bulan." = ".$tahun." = ".$prefix." = ".$inv." = ".$id."\n";

		$urut = str_repeat(0, $maxdigit - strlen($id)) . $id;
		return $urut;
	}

	public function urut($field_code, $table, $prefix, $digit = 4)
	{

		$urutpertama = $prefix . date("ym");

		$latest = DB::table($table)->select($field_code)->where($field_code, 'like', $urutpertama . "%")->orderBy($field_code, 'desc')->get()->first();

		if (empty($latest)) {

			$urutpertama .= $this->urut_id(1, $digit);
		} else {

			$data = substr($latest->kode, -$digit);
			$urutpertama .= $this->urut_id($data + 1, $digit);
		}

		return $urutpertama;
	}

	public function urut_register($type, $provinsi)
	{

		$urutpertama = $type . $this->urut_id($provinsi, 3);

		$latestuser = User::where("kode", 'like', $urutpertama . "%")->orderBy('kode', 'desc')->get()->first();

		$maxdig = 4;

		if (empty($latestuser)) {

			$urutpertama .= $this->urut_id(1, $maxdig);
		} else {

			$data = substr($latestuser->kode, -4);
			$urutpertama .= $this->urut_id($data + 1, $maxdig);
		}

		return $urutpertama;
	}

	public function add_transaction(
		$id = null,
		$name = "",
		$qty = 0,
		$harga_pokok = 0,
		$harga_jual = 0,
		$pv = 0,
		$bv = 0
	) {

		$totalpv = $qty * $pv;
		$totalbv = $qty * $bv;
		$total_price = $harga_jual * $qty;
		$total_pokok = $harga_pokok * $qty;

		$inp = [
			"id" => $id,
			"name" => $name,
			"qty" => $qty,
			"base_price" => $harga_pokok,
			"sell_price" => $harga_jual,
			"pv" => $pv,
			"bv" => $bv,
			"total_pv" => $totalpv,
			"total_bv" => $totalbv,
			"total_base_price" => $total_pokok,
			"total_sell_price" => $total_price,
			"revenue" => $total_price - $total_pokok
		];

		$this->TRANSACTION_DETAIL[] = $inp;
	}

	public function set_karir($userid = null)
	{

		if (!empty($userid)) {
			$this->USERID = $userid;
			$karir = karir::firstOrCreate(['id' => $this->USERID]);
			$this->KARIR['APPV'] = $karir->appv;
			$this->KARIR['APBV'] = $karir->apbv;
			$this->KARIR['PGPV'] = $karir->pgpv;
			$this->KARIR['PGBV'] = $karir->pgbv;
			$this->KARIR[5] = $karir->jbp;
			$this->KARIR[6] = $karir->bj;
			$this->KARIR[7] = $karir->vj;
			$this->KARIR['CAREER_RANK'] = $karir->carrer_rank;
			$this->KARIR['EFFECTIVE_RANK'] = $karir->effective_rank;
			$this->KARIR['PVJ'] = $karir->pv_plan_joy;
			$this->KARIR['PVB'] = $karir->pv_plan_biz;
			$this->KARIR['BVJ'] = $karir->bv_plan_joy;
			$this->KARIR['BVB'] = $karir->bv_plan_biz;
			$this->APPV = $karir->appv;
			$this->APBV = $karir->apbv;
			$this->PGPV = $karir->pgpv;
			$this->PGBV = $karir->pgbv;
			$this->JBP = $karir->jbp;
			$this->BJ = $karir->bj;
			$this->VJ = $karir->vj;
			$this->CAREER_RANK = $karir->carrer_rank;
			//$this->EFFECTIVE_RANK = $karir->effective_rank;
			$this->PVJ = $karir->pv_plan_joy;
			$this->PVB = $karir->pv_plan_biz;
			$this->BVJ = $karir->bv_plan_joy;
			$this->BVB = $karir->bv_plan_biz;

			$period = $this->periode();

			$effective = EffectiveRank::firstOrCreate([
				'id_user_fk' => $this->USERID,
				"date_start" => $period['start'],
				"date_end" => $period['end'],
				"month" => $period['month'],
				"year" => $period['year']
			]);

			$this->EFEKTIF['PGV'] = $effective->pgv;
			$this->EFEKTIF['PPV'] = $effective->ppv;
			$this->EFEKTIF['DATE'] = $effective->date;
			$this->EFEKTIF['EFFECTIVE_RANK'] = $effective->effective_rank;
			$this->EFEKTIF['MONTH'] = $effective->month;
			$this->EFEKTIF['YEAR'] = $effective->year;
			$this->PGV = $effective->pgv;
			$this->PPV = $effective->ppv;
			$this->EFFECTIVE_RANK = $effective->effective_rank;

			if (empty($this->APPV)) {
				$this->calculate_plan();
			}

			if (empty($this->PPV)) {
				$this->calculate_effective();
			}
		}
	}

	public function total_trans_valid()
	{
		$trans_detail = transaksi_detail::with("barang")
			->join("transaction", "transaction.id", "=", "transaction_detail.id_trans_fk")
			->where("transaction.id_cust_fk", $this->USERID)
			->where("transaction.status", "!=", "WP");
		return $trans_detail;
	}

	public function calculate_effective()
	{

		$totalpv = 0;

		$valid_this_month = $this->total_trans_valid()
			->where(DB::raw("date_part('month',created_at)"), date("m"))
			->where(DB::raw("date_part('year',created_at)"), date("Y"))
			->get();

		foreach ($valid_this_month as $a) {
			$totalpv += $a['qty'] * $a['pv'];
		}

		$this->PPV = $totalpv;

		if ($this->DEBUG) {
			echo "TOTAL CALCULATED PPV : " . $totalpv;
			echo "\n";
		}

		return $totalpv;
	}

	public function calculate_plan_debug()
	{

		$detail_trans = collect($this->TRANSACTION_DETAIL);

		$totalpv = $detail_trans->sum("total_pv");

		$totalbv = $detail_trans->sum("total_bv");

		foreach (array("P", "B") as $key => $value) {

			if ($this->DEBUG) {

				echo "TOTAL " . $value . "V LALU : " . $this->KARIR['AP' . $value . 'V'];
				echo "\n";
				echo "TOTAL " . $value . "V JOY : " . $this->KARIR[$value . 'VJ'];
				echo "\n";
				echo "TOTAL " . $value . "V BIZ : " . $this->KARIR[$value . 'VB'];
				echo "\n";
				echo "TOTAL " . $value . "V : " . $totalpv;
				echo "\n";
			}

			$sisa = 0;

			if ($this->KARIR['AP' . $value . 'V'] < 2000) {

				if ($this->DEBUG) {
					echo $value . "V KURANG DARI 2000";
					echo "\n";
				}

				if ($this->KARIR['AP' . $value . 'V'] < 1200) {

					if ($this->DEBUG) {
						echo $value . "V KURANG DARI 1200";
						echo "\n";
					}

					$kebutuhan = 1200 - $this->KARIR['AP' . $value . 'V'];
					$sisa = $totalpv - $kebutuhan;

					if ($this->DEBUG) {

						echo "KEBUTUHAN 1 : " . $kebutuhan;
						echo "\n";
						echo "SISA 1 : " . $sisa;
						echo "\n";
					}

					if ($sisa > 0) {

						$this->KARIR[$value . 'VJ'] += 1200;

						if ($this->DEBUG) {
							echo $value . "VJ + 1200";
							echo "\n";
						}

						if ($sisa > 800) {

							$kebutuhan = 800;
							$sisa = $sisa - $kebutuhan;

							if ($this->DEBUG) {
								echo "KEBUTUHAN 2 : " . $kebutuhan;
								echo "\n";
								echo "SISA 2 : " . $sisa;
								echo "\n";
							}

							if (!empty($sisa)) {

								$x = 800 * 0.8;
								$y = 800 * 0.2;
								$this->KARIR[$value . 'VJ'] += $x;
								$this->KARIR[$value . 'VB'] += $y;

								if ($this->DEBUG) {
									echo $value . "VJ + $x";
									echo "\n";
									echo $value . "VB + $y";
									echo "\n";
								}

								$x =  $sisa * 0.2;
								$y = $sisa * 0.8;

								if ($this->DEBUG) {
									echo $value . "VJ + $x";
									echo "\n";
									echo $value . "VB + $y";
									echo "\n";
								}

								$this->KARIR[$value . 'VJ'] += $x;
								$this->KARIR[$value . 'VB'] += $y;
							} else {

								$x =  $sisa * 0.8;
								$y = $sisa * 0.2;

								$this->KARIR[$value . 'VJ'] += $x;
								$this->KARIR[$value . 'VB'] += $y;

								if ($this->DEBUG) {
									echo $value . "VJ + $x";
									echo "\n";
									echo $value . "VB + $y";
									echo "\n";
								}
							}
						} else {

							$x =  $sisa * 0.8;
							$y = $sisa * 0.2;

							$this->KARIR[$value . 'VJ'] += $x;
							$this->KARIR[$value . 'VB'] += $y;

							if ($this->DEBUG) {
								echo $value . "VJ + $x";
								echo "\n";
								echo $value . "VB + $y";
								echo "\n";
							}
						}
					} else {

						$this->KARIR[$value . 'VJ'] += $totalpv;

						if ($this->DEBUG) {
							echo $value . "VJ + $totalpv";
							echo "\n";
						}
					}
				} else {

					$kebutuhan = 800 - $this->KARIR['AP' . $value . 'V'];
					$sisa = $sisa - $kebutuhan;

					if (!empty($sisa)) {

						$x = 800 * 0.8;
						$y = 800 * 0.2;
						$this->KARIR[$value . 'VJ'] += $x;
						$this->KARIR[$value . 'VB'] += $y;

						if ($this->DEBUG) {
							echo $value . "VJ + $x";
							echo "\n";
							echo $value . "VB + $y";
							echo "\n";
						}

						$x =  $sisa * 0.2;
						$y = $sisa * 0.8;

						if ($this->DEBUG) {
							echo $value . "VJ + $x";
							echo "\n";
							echo $value . "VB + $y";
							echo "\n";
						}

						$this->KARIR[$value . 'VJ'] += $x;
						$this->KARIR[$value . 'VB'] += $y;
					} else {

						$x =  $totalpv * 0.8;
						$y = $totalpv * 0.2;

						$this->KARIR[$value . 'VJ'] += $x;
						$this->KARIR[$value . 'VB'] += $y;

						if ($this->DEBUG) {

							echo $value . "VJ + $x";
							echo "\n";
							echo $value . "VB + $y";
							echo "\n";
						}
					}
				}
			} else {

				$x =  $totalpv * 0.2;
				$y = $totalpv * 0.8;

				$this->KARIR[$value . 'VJ'] += $x;
				$this->KARIR[$value . 'VB'] += $y;

				if ($this->DEBUG) {

					echo $value . "VJ + $x";
					echo "\n";
					echo $value . "VB + $y";
					echo "\n";
				}
			}

			$this->KARIR['AP' . $value . 'V'] += $totalpv;

			if ($this->DEBUG) {

				echo "TOTAL " . $value . "VJ = " . $this->KARIR[$value . 'VJ'];
				echo "\n";
				echo "TOTAL " . $value . "VB = " . $this->KARIR[$value . 'VB'];
				echo "\n";
			}
		}

		$this->APPV = $this->KARIR['APPV'];
		$this->APBV = $this->KARIR['APBV'];
		$this->PVJ = $this->KARIR['PVJ'];
		$this->PVB = $this->KARIR['PVB'];

		return $this->KARIR;
	}

	/*
	public function calculate_plan(){
		$detail_trans = collect($this->TRANSACTION_DETAIL);

		$totalpv = $detail_trans->sum("total_pv");

		$totalbv = $detail_trans->sum("total_bv");

		$totalprice = $detail_trans->sum("sell_price");

		if($this->KARIR['APPV'] < 2000){

		} else {
			$x =  $totalpv*0.2;
			$y = $totalpv*0.8;				

			$this->KARIR['PVJ'] += $totalpv*0.2;
			$this->KARIR['BVJ'] += $totalbv*0.2;

			$this->KARIR['PVB'] += $totalpv*0.8;			
			$this->KARIR['BVB'] += $totalbv*0.8;	
		}

		$this->KARIR['APPV'] += $totalpv;
		$this->KARIR['APBV'] += $totalbv;

		$this->APPV = $this->KARIR['APPV'];
		$this->APBV = $this->KARIR['APBV'];
		$this->PVJ = $this->KARIR['PVJ'];
		$this->PVB = $this->KARIR['PVB'];

		return $this->KARIR;

	}
	*/

	public function calculate_plan()
	{

		$detail_trans = collect($this->TRANSACTION_DETAIL);

		$totalpv = $detail_trans->sum("total_pv");

		$totalbv = $detail_trans->sum("total_bv");

		$totalprice = $detail_trans->sum("sell_price");

		foreach (array("P") as $key => $value) {

			$sisa = 0;

			if ($this->KARIR['AP' . $value . 'V'] < 2000) {

				if ($this->KARIR['AP' . $value . 'V'] < 1200) {

					$kebutuhan = 1200 - $this->KARIR['AP' . $value . 'V'];
					$sisa = $totalpv - $kebutuhan;


					if ($sisa > 0) {

						$this->KARIR[$value . 'VJ'] += 1200;

						if ($sisa > 800) {

							$kebutuhan = 800;
							$pct = 800 / $sisa;

							$sisa = $sisa - $kebutuhan;
							$sisaBV = $sisaBV - ($sisaBV * $pct);


							if (!empty($sisa)) {

								$x = 800 * 0.8;
								$y = 800 * 0.2;
								$this->KARIR[$value . 'VJ'] += $x;
								$this->KARIR[$value . 'VB'] += $y;
								$this->KARIR['BVJ'] += $x;
								$this->KARIR['BVB'] += $y;


								//$x =  $sisa*0.2;
								//$y = $sisa*0.8;
								$x =  $sisa * 0.8;
								$y = $sisa * 0.2;

								$this->KARIR[$value . 'VJ'] += $x;
								$this->KARIR[$value . 'VB'] += $y;
							} else {

								$x =  $sisa * 0.8;
								$y = $sisa * 0.2;

								$this->KARIR[$value . 'VJ'] += $x;
								$this->KARIR[$value . 'VB'] += $y;
							}
						} else {

							$x =  $sisa * 0.8;
							$y = $sisa * 0.2;

							$this->KARIR[$value . 'VJ'] += $x;
							$this->KARIR[$value . 'VB'] += $y;
						}
					} else {

						$this->KARIR[$value . 'VJ'] += $totalpv;
					}
				} else {

					$kebutuhan = 800 - $this->KARIR['AP' . $value . 'V'];
					$sisa = $sisa - $kebutuhan;


					if (!empty($sisa)) {

						$x = 800 * 0.8;
						$y = 800 * 0.2;
						$this->KARIR[$value . 'VJ'] += $x;
						$this->KARIR[$value . 'VB'] += $y;

						//$x =  $sisa*0.2;
						//$y = $sisa*0.8;
						$x =  $sisa * 0.8;
						$y = $sisa * 0.2;


						$this->KARIR[$value . 'VJ'] += $x;
						$this->KARIR[$value . 'VB'] += $y;
					} else {

						$x =  $totalpv * 0.8;
						$y = $totalpv * 0.2;

						$this->KARIR[$value . 'VJ'] += $x;
						$this->KARIR[$value . 'VB'] += $y;
					}
				}
			} else {

				//$x =  $totalpv*0.2;
				//$y = $totalpv*0.8;				
				$x =  $sisa * 0.8;
				$y = $sisa * 0.2;

				$this->KARIR[$value . 'VJ'] += $x;
				$this->KARIR[$value . 'VB'] += $y;
			}

			$this->KARIR['AP' . $value . 'V'] += $totalpv;
		}

		$this->APPV = $this->KARIR['APPV'];
		$this->APBV = $this->KARIR['APBV'];
		$this->PVJ = $this->KARIR['PVJ'];
		$this->PVB = $this->KARIR['PVB'];

		return $this->KARIR;
	}

	public function calculate_plan_ff($id)
	{

		$srank = \App\SRank::where('jbid', $id)->first();
		$APPV = isset($srank) ? $srank->appv : 0;


		$detail_trans = collect($this->TRANSACTION_DETAIL);

		$totalpv = $detail_trans->sum("total_pv");

		$totalbv = $detail_trans->sum("total_bv");

		$totalprice = $detail_trans->sum("sell_price");

		foreach (array("P") as $key => $value) {

			$sisa = 0;

			if ($APPV <= 2000) {
				/*
				if($APPV < 1200){

					$kebutuhan = 1200 - $APPV;
					$sisa = $totalpv - $kebutuhan; 
					

					if($sisa > 0){

						$this->KARIR[$value.'VJ'] += 1200;

						if($sisa > 800){

							$kebutuhan = 800;
							$pct = 800/$sisa;

							$sisa = $sisa - $kebutuhan;
							$sisaBV = $sisaBV - ($sisaBV * $pct);
							

							if($sisa>0){

								$x = 800*0.8;
								$y = 800*0.2;
								$this->KARIR[$value.'VJ'] += $x;
								$this->KARIR[$value.'VB'] += $y;
								$this->KARIR['BVJ'] += $x;
								$this->KARIR['BVB'] += $y;


								//$x =  $sisa*0.2;
								//$y = $sisa*0.8;
								$x =  $sisa*0.8;
								$y = $sisa*0.2;

								$this->KARIR[$value.'VJ'] += $x;
								$this->KARIR[$value.'VB'] += $y;
							} else {

								$x =  $sisa*0.8;
								$y = $sisa*0.2;

								$this->KARIR[$value.'VJ'] += $x;
								$this->KARIR[$value.'VB'] += $y;

							}

						} else {

							$x =  $sisa*0.8;
							$y = $sisa*0.2;

							$this->KARIR[$value.'VJ'] += $x;
							$this->KARIR[$value.'VB'] += $y;

						}

					} else {

						$this->KARIR[$value.'VJ'] += $totalpv;

					}

				} else {

					$kebutuhan = 2000 - $APPV;
					$sisa = $sisa - $kebutuhan;
					

					if($sisa>0){
						//dd($sisa);
						$x = 800*0.8;
						$y = 800*0.2;
						$this->KARIR[$value.'VJ'] += $x;
						$this->KARIR[$value.'VB'] += $y;

						//$x =  $sisa*0.2;
						//$y = $sisa*0.8;
						$x =  $sisa*0.8;
						$y = $sisa*0.2;


						$this->KARIR[$value.'VJ'] += $x;
						$this->KARIR[$value.'VB'] += $y;

					} else {
						//dd($totalpv);
						$x =  $totalpv*0.8;
						$y = $totalpv*0.2;

						$this->KARIR[$value.'VJ'] += $x;
						$this->KARIR[$value.'VB'] += $y;

					}

				}
				*/

				$this->KARIR[$value . 'VJ'] += $totalpv;
				$this->KARIR[$value . 'VB'] += 0;
			} else {

				$x =  $totalpv * 0.8;
				$y = $totalpv * 0.2;

				$this->KARIR[$value . 'VJ'] += $x;
				$this->KARIR[$value . 'VB'] += $y;
			}

			$APPV += $totalpv;
		}

		$this->APPV = $APPV;
		$this->APBV = $this->KARIR['APBV'];
		$this->PVJ = $this->KARIR['PVJ'];
		$this->PVB = $this->KARIR['PVB'];

		return $this->KARIR;
	}

	public function calculate_price()
	{

		$this->PRICE_JOYBIZZER = $this->PRICE * 0.8;

		if ($this->TYPE_USER == 'M') {
			$this->PV = $this->PRICE * 0.4 / $this->PV_PRICE;
			$this->BV = $this->PRICE * 0.4 / $this->BV_PRICE;
		} else {
			$this->PV = $this->PRICE * 0.35 / $this->PV_PRICE;
			$this->BV = $this->PRICE * 0.35 / $this->BV_PRICE;
		}

		switch ($this->LOCATION) {
			case 'WITA':
				$this->PRICE_JOYBIZZER = $this->PRICE_JOYBIZZER * 1.05;
				$this->PRICE = $this->PRICE * 1.05;
				break;
			case 'WIT':
				$this->PRICE_JOYBIZZER = $this->PRICE_JOYBIZZER * 1.1;
				$this->PRICE = $this->PRICE * 1.1;
				break;
			default:

				break;
		}
	}


	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public static function initauth()
	{

		$id = null;
		$user = Auth::user();
		$id = $user->uuid;


		$userlogin = [];
		if ($id) {
			$userlogin = Member::where("uuid", $id)
				->with('address')
				->first();

			$userlogin['totNeedUpline'] = Member::where([
				['sponsor_uuid', $id],
				['placement', null],
				['status', 1],
				// ['flag', 1]
			])->count();

			$voucher = Joybiz::getSaldoVoucher($userlogin->uuid);
			$userlogin['voucher'] = is_null($voucher) ? 0 : decrypt($voucher);

			$wallet = Wallet::where('member_uuid', $userlogin->uid)->first();
			$userlogin['wallet'] = is_null($wallet) ? 0 : decrypt($wallet->saldo);

			$Challenge = new Challenge;
			$challenges = $Challenge->YoungEagleDetail($id);

			$totalYoungEagle = 0;
			foreach ($challenges as $c) {
				$totalYoungEagle += $c["counter"];
			}
			$userlogin['youngeagle'] = $totalYoungEagle;
			//$userlogin['youngeagle'] = $Challenge->YoungEagle($id);


			if ($userlogin->activated_at) {
				$ExpressExpiredDate = Carbon::createFromFormat('Y-m-d', $userlogin->activated_at)->addMonths(3);
				$ExpressExpired = $ExpressExpiredDate->lt(Carbon::now());

				$userlogin['ExpressExpired'] = $ExpressExpired;
				$userlogin['ExpressExpiredDate'] = $ExpressExpiredDate->subDay();
			}
		}

		return $userlogin;
	}



	public static function baseproduct()
	{

		$userlogin = Joybiz::initauth();

		$wilayah = (isset($userlogin->alamat_provinsi->kelompok)) ? $userlogin->alamat_provinsi->kelompok : 1;

		$acc = (isset($userlogin) && !empty($userlogin))
			? "harga_" . $wilayah
			: "harga_retail_" . $wilayah;


		$bv = ($userlogin && $userlogin->flag == 2) ? "bv_sc" : "bv";
		//tentukan yang berlaku harga joybizer atau retail				

		$x = barang::select(
			"barang.id as barang_id",
			DB::raw(" null as stock_detail_id"),
			DB::raw(" null as stock_id"),
			"barang.nama",
			"barang.kode",
			"barang.id_category_fk",
			"barang.pv",
			"barang." . $bv . " as bv",
			"barang." . $acc . " as base_price",
			"barang." . $acc . " as harga",
			"barang.harga_1",
			"barang.harga_2",
			"barang.harga_3",
			"barang.harga_retail_1",
			"barang.harga_retail_2",
			"barang.harga_retail_3",
			"barang.weight",
			"barang.shipping_budget",
			"barang.status",
			"barang.is_register",
			"barang.is_show",
			"barang.index",
			DB::raw("SUM(stock_detail.jumlah) as stock"),
			"barang.desc",
			"category.title as kategori",
			"jenis_barang.nama as jenis",
			"media.link as pict"
		)
			->distinct()
			->leftjoin("stock", "stock.id_barang_fk", "=", "barang.id")
			->leftjoin("media", "media.id_barang_fk", "=", "barang.id")
			->leftjoin("category", "barang.id_category_fk", "=", "category.id")
			->leftjoin("jenis_barang", "barang.id_jenis_fk", "=", "jenis_barang.id")
			->leftjoin("stock_detail", "stock_detail.id_stock_fk", "=", "stock.id")
			->groupBy("barang.id", "stock_detail.id", "stock.id", "category.title", "jenis_barang.nama", "media.link")
			->orderBy('barang.index', 'asc');


		return $x;
	}

	public static function paket_daftar()
	{
		return Joybiz::baseproduct()
			->where("barang.is_register", '>', 0);
	}

	public static function basedetailproduct()
	{

		$userlogin = Joybiz::initauth();

		$wilayah = (isset($userlogin->alamat_provinsi->kelompok)) ?
			$userlogin->alamat_provinsi->kelompok : 1;

		//$acc = (isset($userlogin->flag) && !empty($userlogin->flag) && $userlogin->flag == 1) ? 
		$acc = (isset($userlogin) && !empty($userlogin)) ? "harga_" . $wilayah : "harga_retail_" . $wilayah;

		$x = stock_detail::select(
			"stock_detail.id as stock_detail_id",
			"stock.id as stock_id",
			"barang.id as barang_id",
			"barang.pv",
			"barang.bv",
			"barang.nama",
			"barang.kode",
			"stock_detail.jumlah as stock",
			"stock_detail.harga_pokok as base_price",
			"barang." . $acc . " as harga",
			"barang.harga_1",
			"barang.harga_2",
			"barang.harga_3",
			"barang.harga_retail_1",
			"barang.harga_retail_2",
			"barang.harga_retail_3",
			"barang.desc",
			"media.link as pict",
			"stock_detail.barcode",
			"jenis_barang.nama as jenis"
		)
			->leftjoin("stock", "stock.id", "=", "stock_detail.id_stock_fk")
			->join("barang", "barang.id", "=", "stock.id_barang_fk")
			->leftjoin("media", "barang.id", "=", "media.id_barang_fk")
			->leftjoin("category", "barang.id_category_fk", "=", "category.id")
			->leftjoin("jenis_barang", "barang.id_jenis_fk", "=", "jenis_barang.id");


		return $x;
	}

	public function update_upline()
	{
		$user = new User;
		$check = $user->findtree($this->USERID, "upline")->toArray();

		$user->generate_level($check, 1, 1, 1, "upline");

		foreach ($user->user_level as $key => $value) {
			$karir = karir::where("id", $value)->first();
			switch ($this->CAREER_RANK) {
				case "JBP":
					$karir->jbp += 1;
					break;
				case "VJ":
					$karir->vj += 1;
					break;
				case "BJ":
					$karir->bj += 1;
					break;
			}

			$karir->save();

			$joybizz = new Joybiz;

			$joybizz->set_karir($value);

			$joybizz->save_karir($value);
		}
	}

	public function periode()
	{
		$ret = [];
		$period = new \DatePeriod(
			new \DateTime(date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")))),
			new \DateInterval('P1D'),
			new \DateTime(date("Y-m-d"))
		);

		foreach ($period as $key => $value) {
			if ($value->format("N") == config("app.week_periode_day")) {
				$next_week = strtotime("+6 days", $value->format("U"));
				$ret = [
					"start" => $value->format("Y-m-d"),
					"end" => date("Y-m-d", $next_week),
					"year" => $value->format("Y"),
					"month" => $value->format("m")
				];
			}
		}

		return $ret;
	}

	public function save_karir()
	{

		if ($this->USERID) {

			$this->calculate_career_rank();
			$this->calculate_effective_rank();

			$karir = karir::firstOrCreate(['id' => $this->USERID]);

			$karir->appv = $this->APPV;
			$karir->apbv = $this->APBV;

			$karir->pv_plan_joy = $this->PVJ;
			$karir->pv_plan_biz = $this->PVB;
			$karir->bv_plan_joy = $this->BVJ;
			$karir->bv_plan_biz = $this->BVB;
			$karir->carrer_rank = $this->CAREER_RANK;
			$karir->effective_rank = $this->EFFECTIVE_RANK;

			$karir->save();

			$period = $this->periode();

			$effective_check = EffectiveRank::firstOrCreate([
				'id_user_fk' => $this->USERID,
				"date_start" => $period['start'],
				"date_end" => $period['end'],
				"month" => $period['month'],
				"year" => $period['year']
			]);

			$effective_check->ppv = $this->PPV;
			$effective_check->pgv = $this->PGV;
			$effective_check->effective_rank = $this->EFFECTIVE_RANK;

			$effective_check->save();
		}
	}


	public function calculate_effective_rank()
	{

		if ($this->DEBUG) {
			echo "CALCULATE EFFECTIVE RANK : \n";
			echo "UID:" . $this->USERID . "\nPPV:" . $this->PPV;
			echo "\n";
		}

		$up = [1600, 1200, 1000, 800, 600, 500, 100];
		$uid = [];
		$current_downline = [];

		if (
			$this->PPV >= 12
			// && $this->PPV < 30
			&& $this->CAREER_RANK >= 1
		) {
			$this->EFFECTIVE_RANK = 1;
		}
		if (
			$this->PPV >= 30
			// && $this->PPV < 60
			&& $this->CAREER_RANK >= 2
		) {
			$this->EFFECTIVE_RANK = 2;
		}
		if (
			$this->PPV >= 60
			// && $this->PPV < 80
			&& $this->CAREER_RANK >= 3
		) {
			$this->EFFECTIVE_RANK = 3;
		}
		if (
			$this->PPV >= 80
			// && $this->PPV < 100
			&& $this->CAREER_RANK >= 4
		) {
			$this->EFFECTIVE_RANK = 4;
		}
		if (
			$this->PPV >= 100
			&& $this->CAREER_RANK >= 5
		) {

			$this->EFFECTIVE_RANK = 5;
			$last_month_time = mktime(0, 0, 0, date("m") - 1, 1, date("Y"));
			$last_month = date("m", $last_month_time);
			$last_year = date("Y", $last_month_time);
			$last_month_effective = EffectiveRank::where("year", $last_year)
				->where("month", $last_month)
				->where("id_user_fk", $this->USERID)->first();

			foreach ($up as $key => $value) {

				$current_downline[$value] = posisi::join("effective_rank", "effective_rank.id_user_fk", "=", "posisi.id_downline_fk")
					->where("posisi.id_user_fk", $this->USERID)
					->whereNotIn("id_downline_fk", $uid)
					->where("effective_rank.month", date("m"))
					->where("effective_rank.year", date("Y"))
					->where("effective_rank.ppv", ">=", $value)
					->get()
					->pluck("id_downline_fk")
					->toArray();

				$uid = array_merge($uid, $current_downline[$value]);
			}

			$qualified = false;


			if (count($current_downline[100]) >= 2) {
				$this->EFFECTIVE_RANK = 6;
				$qualified = true;
			}
			if (count($current_downline[1000]) >= 2 && count($current_downline[500]) >= 1) {
				$this->EFFECTIVE_RANK = 7;
				$qualified = true;
			}

			if (count($current_downline[1200]) >= 2 && count($current_downline[600]) >= 4) {
				$this->EFFECTIVE_RANK = 8;
				$qualified = true;
			}

			if (count($current_downline[1600]) >= 2 && count($current_downline[800]) >= 5) {
				$this->EFFECTIVE_RANK = 9;
				$qualified = true;
			}

			if (count($current_downline[1600]) >= 5 && count($current_downline[800]) >= 6) {
				$this->EFFECTIVE_RANK = 10;
				$qualified = true;
			}

			if (count($current_downline[1600]) >= 6 && count($current_downline[800]) >= 7) {
				$this->EFFECTIVE_RANK = 11;
				$qualified = true;
			}

			if (count($current_downline[1600]) >= 7 && count($current_downline[800]) >= 8) {
				$this->EFFECTIVE_RANK = 12;
				$qualified = true;
			}

			if (!$qualified) {

				if ($last_month_effective) {
					$this->EFFECTIVE_RANK = ($this->EFFECTIVE_RANK >
						$last_month_effective->effective_rank) ?
						$this->CAREER_RANK : $last_month_effective->effective_rank;
				}
			}
		}

		if ($this->DEBUG) {

			echo "UID:" . $this->USERID . "\nCAREER:" . $this->CAREER_RANK . "\nPPV:" . $this->PPV . "\nEFFECTIVE:" . $this->EFFECTIVE_RANK . "\nDOWNLINE:";
			print_r($current_downline);
			echo "\n";
		}
	}

	public function hitung($pencarian, $min1, $min2, $result)
	{
		if ($pencarian >= $min1) {
			if ($this->DEBUG) {
				echo "TERPENUHI";
				echo "\n";
			}
			if ($this->JBP >= $min2) {
				if ($this->DEBUG) {
					echo "JBP TERPENUHI";
					echo "\n";
				}
				$this->CAREER_RANK = $result;
			} else {
				$sisa = $this->BJ + $this->VJ;
				if ($this->DEBUG) {
					echo "TIDAK TERPENUHI. SISA " . $sisa;
					echo "\n";
				}
				if ($sisa + $this->JBP >= $min2) {
					$this->CAREER_RANK = $result;
					if ($this->DEBUG) {
						echo "TIDAK TERPENUHI. SISA " . $sisa;
						echo "\n";
					}
				}
			}
		}
	}

	public function calculate_career_rank()
	{

		if ($this->DEBUG) {
			echo "CALCULATE CAREER RANK : \n";
			echo "UID:" . $this->USERID . "\nAPPV:" . $this->APPV;
			echo "\n";
		}

		if (
			$this->APPV >= 20
			//&& $this->APPV < 80
		) {
			$this->CAREER_RANK = 1;
		}
		if (
			$this->APPV >= 80
			//&& $this->APPV < 400
		) {
			$this->CAREER_RANK = 2;
		}
		if (
			$this->APPV >= 400
			//&& $this->APPV < 1200
		) {
			$this->CAREER_RANK = 3;
		}
		if (
			$this->APPV >= 1200
			//&& $this->APPV < 2000
		) {
			$this->CAREER_RANK = 4;
		}
		if ($this->APPV >= 2000) {

			$this->CAREER_RANK = 5;

			$downline = posisi::select(["id_downline_fk", "carrer_rank"])
				->join("karir", "karir.id", "=", "posisi.id_downline_fk")
				->where("posisi.id_user_fk", $this->USERID)
				->where("lvl", 1)->get();

			if ($this->DEBUG) {
				echo "FIRST DOWNLINE : ";
				print_r($downline->toArray());
			}

			$header = [];

			$ada_jbp = false;

			$total_jbp_head = 0;

			foreach ($downline as $key => $value) {

				$r = $value->carrer_rank;

				$header[$r] = (isset($header[$r])) ? $header[$r] + 1 : 1;

				$downline2 = posisi::select(["id_downline_fk", "carrer_rank"])
					->join("karir", "karir.id", "=", "posisi.id_downline_fk")
					->where("posisi.id_user_fk", $value->id_downline_fk)
					->get();

				$up = [];
				if ($this->DEBUG) {
					echo "CHILD DOWNLINE : ";
					print_r($downline2->toArray());
				}
				foreach ($downline2 as $key2 => $value2) {

					$r = $value2->carrer_rank;
					$header[$r] = (isset($header[$r])) ? $header[$r] + 1 : 1;
				}
			}

			foreach ($header as $key => $value) {
				$this->JBP = (isset($value[5])) ? $value[5] : 0;
				$this->BJ = (isset($value[6])) ? $value[6] : 0;
				$this->VJ = (isset($value[7])) ? $value[7] : 0;
			}

			if ($this->DEBUG) {
				echo "BJ:" . $this->BJ . " JBP:" . $this->JBP . " VJ:" . $this->VJ;
				echo "\n";
			}
			if ($this->KARIR['CAREER_RANK'] == 4) {
				//$this->update_upline();
			}

			if ($this->JBP >= 2) {
				echo "UPGRADE TO BJ";
				echo "\n";
				$this->CAREER_RANK = 6;
				//if($this->KARIR['CAREER_RANK'] == 5){
				//$this->update_upline();
				//}
			}

			$this->hitung($this->BJ, 2, 3, 7);
			$this->hitung($this->VJ, 2, 3, 8);
			$this->hitung($this->VJ, 4, 3, 9);
			$this->hitung($this->VJ, 8, 6, 10);
			$this->hitung($this->VJ, 12, 7, 11);
			$this->hitung($this->VJ, 16, 8, 12);

			if ($this->DEBUG) {
				echo "FINAL : " . $this->USERID . " = " . $this->CAREER_RANK . "\n";
			}
		}
	}


	public function check_Rank($appv, $leg_jbp, $bj, $vj)
	{
		if ($appv < 2000) {
			if ($appv < 20) {
				$rank = 0;
			} else if ($appv < 80) {
				$rank = 1;
			} else if ($appv < 400) {
				$rank = 2;
			} else if ($appv < 1200) {
				$rank = 3;
			} else if ($appv < 2000) {
				$rank = 4;
			}
		} else {
			if ($leg_jbp >= 8 && $vj >= 16) {
				$rank = 12;
			} else if ($leg_jbp >= 7 && $vj >= 12) {
				$rank = 11;
			} else if ($leg_jbp >= 6 && $vj >= 8) {
				$rank = 10;
			} else if ($leg_jbp >= 3 && $vj >= 4) {
				$rank = 9;
			} else if ($leg_jbp >= 3 && $vj >= 2) {
				$rank = 8;
			} else if ($leg_jbp >= 3 && $bj >= 2) {
				$rank = 7;
			} else if ($leg_jbp >= 2) {
				$rank = 6;
			} else {
				$rank = 5;
			}
		}

		return $rank;
	}

	public function check_eRank($jbid, $ppv, $mid)
	{
		$srank = SRank::where('jbid', $jbid)->first();
		$rank = 0;


		if ($ppv < 100) {
			if ($ppv < 12) {
				$rank = 0;
			} else if ($ppv < 30) {
				$rank = 1;
			} else if ($ppv < 60) {
				$rank = 2;
			} else if ($ppv < 80) {
				$rank = 3;
			} else if ($ppv < 100) {
				$rank = 4;
			}
		} else {
			$leg1600 = ERank::where([['upid', $jbid], ['mid', $mid], ['gpv', '>=', 1600]])->count();
			$less1600 = ERank::where([['upid', $jbid], ['mid', $mid], ['gpv', '<', 1600]])->sum('gpv');

			$leg1200 = ERank::where([['upid', $jbid], ['mid', $mid], ['gpv', '>=', 1200]])->count();
			$less1200 = ERank::where([['upid', $jbid], ['mid', $mid], ['gpv', '<', 1200]])->sum('gpv');

			$leg1000 = ERank::where([['upid', $jbid], ['mid', $mid], ['gpv', '>=', 1000]])->count();
			$less1000 = ERank::where([['upid', $jbid], ['mid', $mid], ['gpv', '<', 1000]])->sum('gpv');

			$leg100 = ERank::where([['upid', $jbid], ['mid', $mid], ['gpv', '>=', 100]])->count();

			if ($leg1600 >= 7 && ($less1600 + (($leg1600 - 7) * 1600)) >= 800) {
				$rank = 12;
			} else if ($leg1600 >= 6 && ($less1600 + (($leg1600 - 6) * 1600)) >= 800) {
				$rank = 11;
			} else if ($leg1600 >= 5 && ($less1600 + (($leg1600 - 5) * 1600)) >= 800) {
				$rank = 10;
			} else if ($leg1600 >= 2 && ($less1600 + (($leg1600 - 2) * 1600)) >= 800) {
				$rank = 9;
			} else if ($leg1200 >= 2 && ($less1200 + (($leg1200 - 2) * 1200)) >= 600) {
				$rank = 8;
			} else if ($leg1000 >= 2 && ($less1000 + (($leg1000 - 2) * 1000)) >= 500) {
				$rank = 7;
			} else if ($leg100 >= 2) {
				$rank = 6;
			} else {
				$rank = 5;
			}
		}

		//if effective rank larger then status rank, then set rank with status rank
		if ($rank > $srank->srank) $rank = $srank->srank;
		return $rank;
	}

	//$jbid, $ppv, $pbv, $gpvplus, $updated_at, $jbp, $bj, $vj, $ppvj, $pbvj, $gpvj, $gbvj, $ppvb, $pbvb, $gpvb, $gbvb 
	public function update_Plan_Data($jbid, $ppv, $pbv, $gpv, $gbv, $transaction_date, $jbp, $bj, $vj, $ppvj, $pbvj, $gpvj, $gbvj, $ppvb, $pbvb, $gpvb, $gbvb, $omzet)
	{
		$user = User::select('id', 'id_sponsor_fk', 'id_upline_fk')->where('id', $jbid)->first();

		if ($user) {
			$spid = $user->id_sponsor_fk;
			$upid = $user->id_upline_fk;

			$mid = date('Ym', strtotime($transaction_date));
			$wid = Period::where([['sDate', '<=', $transaction_date], ['eDate', '>=', $transaction_date]])->first(['id']);

			/* update status rank */
			$srank = SRank::firstOrCreate(['jbid' => $jbid], ['spid' => $spid, 'upid' => $upid]);
			$leg_jbp = SRank::where([['upid', $jbid], ['jbp', '>', 0]])->orWhere([['upid', $jbid], ['srank', 5]])->count();
			$current_rank = $srank->srank;

			$newRank = $this->check_Rank($srank->appv + $ppv, $leg_jbp, $srank->bj, $srank->vj);

			$srank->appv += $ppv;
			$srank->jbp += $jbp;
			$srank->bj += $bj;
			$srank->vj += $vj;
			$srank->srank = $newRank;
			$srank->save();


			if (($newRank >= 5) && ($newRank > $current_rank)) {
				switch ($current_rank) {
					case 5:
						$jbp -= 1;
						break;
					case 6:
						$bj -= 1;
						break;
				}

				switch ($newRank) {
					case 5:
						$jbp += 1;
						break; //$this->update_Status_Rank($upid,0,1,0,0); break;
					case 6:
						$bj += 1;
						break; //$this->update_Status_Rank($upid,0,0,1,0); break;
					case 7:
						$vj += 1;
						break; //$this->update_Status_Rank($upid,0,0,0,1); break;
				}
			}

			/* update status rank done */

			$erank = ERank::firstOrCreate(['jbid' => $jbid, 'mid' => $mid], ['spid' => $spid, 'upid' => $upid]);
			$current_erank = $erank->erank;
			$erank->ppv += $ppv;
			$erank->gpv += $gpv;
			$erank->erank = $this->check_eRank($jbid, $erank->ppv + $ppv, $mid);
			$erank->save();


			$Prepared_Data_Joy = PreparedDataJoy::firstOrCreate(['jbid' => $jbid, 'wid' => $wid->id], ['spid' => $spid, 'upid' => $upid]);
			/*if ($jbid == 2){
				echo "Last GPVJ : ".$Prepared_Data_Joy->gpvj . " + GPVJ : " . $gpvj ."\n";
			}*/
			$Prepared_Data_Joy->ppv += $ppv;
			$Prepared_Data_Joy->pbv += $pbv;
			$Prepared_Data_Joy->ppvj += $ppvj;
			$Prepared_Data_Joy->pbvj += $pbvj;
			$Prepared_Data_Joy->gpvj += $gpvj;
			$Prepared_Data_Joy->gbvj += $gbvj;
			$Prepared_Data_Joy->omzet += $omzet;
			$Prepared_Data_Joy->srank = $srank->srank;
			$Prepared_Data_Joy->erank = $erank->erank;
			$Prepared_Data_Joy->save();

			//if ppvb or gpvb not null
			if ($ppvb || $gpvb) {
				$Prepared_Data_Biz = PreparedDataBiz::firstOrCreate(['jbid' => $jbid, 'mid' => $mid], ['spid' => $spid, 'upid' => $upid]);
				$Prepared_Data_Biz->ppv += $ppv;
				$Prepared_Data_Biz->pbv += $pbv;
				$Prepared_Data_Biz->gpv += $gpv;
				$Prepared_Data_Biz->gbv += $gbv;
				$Prepared_Data_Biz->ppvb += $ppvb;
				$Prepared_Data_Biz->pbvb += $pbvb;
				$Prepared_Data_Biz->gpvb += $gpvb;
				$Prepared_Data_Biz->gbvb += $gbvb;
				$Prepared_Data_Biz->omzet += $omzet;
				$Prepared_Data_Biz->srank = $srank->srank;
				$Prepared_Data_Biz->erank = $erank->erank;
				$Prepared_Data_Biz->save();
			}

			/* :D */
			$ppv  += $gpv;
			$pbv  += $gbv;
			$ppvj += $gpvj;
			$pbvj += $gbvj;
			$ppvb += $gpvb;
			$pbvb += $gbvb;
			/* :D */

			$this->update_Effective_Rank($upid, 0, 0, $ppv, $pbv, $updated_at, $jbp, $bj, $vj, 0, 0, $ppvj, $pbvj, 0, 0, $ppvb, $pbvb, $omzet);
		}
	}

	//update Joy & Biz Plan requirement data
	public function update_Effective_Rank_Sim($jbid, $ppv, $pbv, $gpv, $gbv, $updated_at, $jbp, $bj, $vj, $ppvj, $pbvj, $gpvj, $gbvj, $ppvb, $pbvb, $gpvb, $gbvb, $omzet, $ozj, $ozb)
	{
		$user = User::select('id', 'id_sponsor_fk', 'id_upline_fk')->where('id', $jbid)->first();

		if ($user) {
			$spid = $user->id_sponsor_fk;
			$upid = $user->id_upline_fk;

			//$mid = date('Ym',strtotime($updated_at));
			$wid = Period::where([['sDate', '<=', $updated_at], ['eDate', '>=', $updated_at]])->first(['id', 'sDate', 'eDate']);
			//$mid = date('Ym',strtotime($wid->sDate));
			$mid = date('Ym', strtotime($wid->eDate));

			/* update status rank */
			$srank = SRank::firstOrCreate(['jbid' => $jbid], ['spid' => $spid, 'upid' => $upid]);
			$leg_jbp = SRank::where([['upid', $jbid], ['jbp', '>', 0]])->orWhere([['upid', $jbid], ['srank', 5]])->count();
			$current_rank = is_null($srank->srank) ? 0 : $srank->srank;

			$srank_downlines = SRank::where('upid', $jbid)->get();
			$tot_bj = 0;
			$tot_vj = 0;

			$tot_vj_temp = 0;
			$tot_bj_temp = 0;

			foreach ($srank_downlines as $srank_downline) {
				if ($srank_downline->srank == 6) {
					$tot_bj_temp = 1 + $srank_downline->bj;
				} else {
					$tot_bj_temp = $srank_downline->bj;
				}

				if ($srank_downline->srank >= 7) {
					$tot_vj_temp = 1 + $srank_downline->vj;
				} else {
					$tot_vj_temp = $srank_downline->vj;
				}


				$tot_vj += ($tot_vj_temp > 3) ? 3 : $tot_vj_temp;
				$tot_bj += ($tot_bj_temp > 3) ? 3 : $tot_bj_temp;
			}


			//$newRank = $this->check_Rank($srank->appv + $ppv,$leg_jbp,$srank->bj,$srank->vj);					
			$newRank = $this->check_Rank($srank->appv + $ppv, $leg_jbp, $tot_bj, $tot_vj);

			$srank->appv += $ppv;
			$srank->jbp += $jbp;
			$srank->bj += $bj;
			$srank->vj += $vj;
			$srank->srank = $newRank;
			$srank->save();


			if (($newRank >= 5) && ($newRank > $current_rank)) {
				switch ($current_rank) {
					case 5:
						$jbp -= 1;
						break;
					case 6:
						$bj -= 1;
						break;
				}

				switch ($newRank) {
					case 5:
						$jbp += 1;
						break; //$this->update_Status_Rank($upid,0,1,0,0); break;
					case 6:
						$bj += 1;
						break; //$this->update_Status_Rank($upid,0,0,1,0); break;
					case 7:
						$vj += 1;
						break; //$this->update_Status_Rank($upid,0,0,0,1); break;
				}
			}

			/* update status rank done */

			$erank = ERank::firstOrCreate(['jbid' => $jbid, 'mid' => $mid], ['spid' => $spid, 'upid' => $upid]);
			$current_erank = is_null($erank->erank) ? 0 : $erank->erank;
			$erank->ppv += $ppv;
			$erank->gpv += $gpv;
			$erank->erank = $this->check_eRank($jbid, $erank->ppv + $ppv, $mid);
			$erank->save();


			$Prepared_Data_Joy = PreparedDataJoy::firstOrCreate(['jbid' => $jbid, 'wid' => $wid->id], ['spid' => $spid, 'upid' => $upid]);
			/*if ($jbid == 2){
				echo "Last GPVJ : ".$Prepared_Data_Joy->gpvj . " + GPVJ : " . $gpvj ."\n";
			}*/
			$Prepared_Data_Joy->ppv += $ppv;
			$Prepared_Data_Joy->pbv += $pbv;
			$Prepared_Data_Joy->ppvj += $ppvj;
			$Prepared_Data_Joy->pbvj += $pbvj;
			$Prepared_Data_Joy->gpvj += $gpvj;
			$Prepared_Data_Joy->gbvj += $gbvj;
			$Prepared_Data_Joy->omzet += $omzet;
			$Prepared_Data_Joy->ozj += $ozj;
			$Prepared_Data_Joy->srank = $srank->srank;
			$Prepared_Data_Joy->erank = $erank->erank;
			$Prepared_Data_Joy->save();

			//if ppvb or gpvb not null
			if ($ppvb || $gpvb) {
				$Prepared_Data_Biz = PreparedDataBiz::firstOrCreate(['jbid' => $jbid, 'mid' => $mid], ['spid' => $spid, 'upid' => $upid]);
				$Prepared_Data_Biz->ppv += $ppv;
				$Prepared_Data_Biz->pbv += $pbv;
				$Prepared_Data_Biz->gpv += $gpv;
				$Prepared_Data_Biz->gbv += $gbv;
				$Prepared_Data_Biz->ppvb += $ppvb;
				$Prepared_Data_Biz->pbvb += $pbvb;
				$Prepared_Data_Biz->gpvb += $gpvb;
				$Prepared_Data_Biz->gbvb += $gbvb;
				$Prepared_Data_Biz->omzet += $omzet;
				$Prepared_Data_Biz->ozb += $ozb;
				$Prepared_Data_Biz->srank = $srank->srank;
				$Prepared_Data_Biz->erank = $erank->erank;
				$Prepared_Data_Biz->save();
			}

			/* :D */
			$ppv  += $gpv;
			$pbv  += $gbv;
			$ppvj += $gpvj;
			$pbvj += $gbvj;
			$ppvb += $gpvb;
			$pbvb += $gbvb;
			/* :D */

			$this->update_Effective_Rank_Sim($upid, 0, 0, $ppv, $pbv, $updated_at, $jbp, $bj, $vj, 0, 0, $ppvj, $pbvj, 0, 0, $ppvb, $pbvb, $omzet, $ozj, $ozb);
		}
	}

	//update Joy & Biz Plan requirement data
	public function resync_plan_data($jbid, $ppv, $pbv, $gpv, $gbv, $updated_at, $jbp, $bj, $vj, $ppvj, $pbvj, $gpvj, $gbvj, $ppvb, $pbvb, $gpvb, $gbvb, $omzet, $ozj, $ozb)
	{
		$user = User::select('id', 'id_sponsor_fk', 'id_upline_fk')->where('id', $jbid)->first();

		if ($user) {
			$spid = $user->id_sponsor_fk;
			$upid = $user->id_upline_fk;

			$mid = date('Ym', strtotime($updated_at));
			$wid = Period::where([['sDate', '<=', $updated_at], ['eDate', '>=', $updated_at]])->first(['id']);

			/* update status rank */
			$srank = SRank::firstOrCreate(['jbid' => $jbid], ['spid' => $spid, 'upid' => $upid]);
			$leg_jbp = SRank::where([['upid', $jbid], ['jbp', '>', 0]])->orWhere([['upid', $jbid], ['srank', 5]])->count();
			$current_rank = is_null($srank->srank) ? 0 : $srank->srank;

			//$newRank = $this->check_Rank($srank->appv + $ppv,$leg_jbp,$srank->bj,$srank->vj);					
			$newRank = $this->check_Rank($srank->appv, $leg_jbp, $srank->bj, $srank->vj);

			//$srank->appv += $ppv;
			$srank->jbp += $jbp;
			$srank->bj += $bj;
			$srank->vj += $vj;
			$srank->srank = $newRank;
			$srank->save();


			if (($newRank >= 5) && ($newRank > $current_rank)) {
				switch ($current_rank) {
					case 5:
						$jbp -= 1;
						break;
					case 6:
						$bj -= 1;
						break;
				}

				switch ($newRank) {
					case 5:
						$jbp += 1;
						break; //$this->update_Status_Rank($upid,0,1,0,0); break;
					case 6:
						$bj += 1;
						break; //$this->update_Status_Rank($upid,0,0,1,0); break;
					case 7:
						$vj += 1;
						break; //$this->update_Status_Rank($upid,0,0,0,1); break;
				}
			}

			/* update status rank done */

			$erank = ERank::firstOrCreate(['jbid' => $jbid, 'mid' => $mid], ['spid' => $spid, 'upid' => $upid]);
			$current_erank = is_null($erank->erank) ? 0 : $erank->erank;
			//$erank->ppv += $ppv;
			$erank->gpv += $gpv;
			$erank->erank = $this->check_eRank($jbid, $erank->ppv, $mid); //$this->check_eRank($jbid,$erank->ppv+$ppv,$mid);
			$erank->save();


			$Prepared_Data_Joy = PreparedDataJoy::firstOrCreate(['jbid' => $jbid, 'wid' => $wid->id], ['spid' => $spid, 'upid' => $upid]);
			/*if ($jbid == 2){
				echo "Last GPVJ : ".$Prepared_Data_Joy->gpvj . " + GPVJ : " . $gpvj ."\n";
			}*/
			$Prepared_Data_Joy->ppv += $ppv;
			$Prepared_Data_Joy->pbv += $pbv;
			$Prepared_Data_Joy->ppvj += $ppvj;
			$Prepared_Data_Joy->pbvj += $pbvj;
			$Prepared_Data_Joy->gpvj += $gpvj;
			$Prepared_Data_Joy->gbvj += $gbvj;
			$Prepared_Data_Joy->omzet += $omzet;
			$Prepared_Data_Joy->ozj += $ozj;
			$Prepared_Data_Joy->srank = $srank->srank;
			$Prepared_Data_Joy->erank = $erank->erank;
			$Prepared_Data_Joy->save();

			//if ppvb or gpvb not null
			if ($ppvb || $gpvb) {
				$Prepared_Data_Biz = PreparedDataBiz::firstOrCreate(['jbid' => $jbid, 'mid' => $mid], ['spid' => $spid, 'upid' => $upid]);
				$Prepared_Data_Biz->ppv += $ppv;
				$Prepared_Data_Biz->pbv += $pbv;
				$Prepared_Data_Biz->gpv += $gpv;
				$Prepared_Data_Biz->gbv += $gbv;
				$Prepared_Data_Biz->ppvb += $ppvb;
				$Prepared_Data_Biz->pbvb += $pbvb;
				$Prepared_Data_Biz->gpvb += $gpvb;
				$Prepared_Data_Biz->gbvb += $gbvb;
				$Prepared_Data_Biz->omzet += $omzet;
				$Prepared_Data_Biz->ozb += $ozb;
				$Prepared_Data_Biz->srank = $srank->srank;
				$Prepared_Data_Biz->erank = $erank->erank;
				$Prepared_Data_Biz->save();
			}

			/* :D */
			$ppv  += $gpv;
			$pbv  += $gbv;
			$ppvj += $gpvj;
			$pbvj += $gbvj;
			$ppvb += $gpvb;
			$pbvb += $gbvb;
			/* :D */

			$this->resync_plan_data($upid, 0, 0, $ppv, $pbv, $updated_at, $jbp, $bj, $vj, 0, 0, $ppvj, $pbvj, 0, 0, $ppvb, $pbvb, $omzet, $ozj, $ozb);
		}
	}


	public function recheck_Effective_Rank($jbid, $ppv, $gpvplus, $updated_at, $jbp, $bj, $vj)
	{
		$user = User::select('id', 'id_sponsor_fk', 'id_upline_fk')->where('id', $jbid)->first();
		$spid = $user->id_sponsor_fk;
		$upid = $user->id_upline_fk;
		$mid = date('n', strtotime($updated_at));

		/* update status rank */
		$srank = SRank::firstOrCreate(['jbid' => $jbid], ['spid' => $spid, 'upid' => $upid]);
		$leg_jbp = SRank::where([['upid', $jbid], ['jbp', '>', 0]])->count();
		$current_rank = $srank->srank;

		$newRank = $this->check_Rank($srank->appv, $leg_jbp, $srank->bj, $srank->vj);

		$srank->appv -= $ppv;
		$srank->jbp -= $jbp;
		$srank->bj -= $bj;
		$srank->vj -= $vj;
		$srank->srank = $newRank;
		$srank->save();

		if (($newRank < 7) && ($newRank < $current_rank)) {
			switch ($current_rank) {
				case 5:
					$jbp -= 1;
					break;
				case 6:
					$bj -= 1;
					break;
				default:
					$vj -= 1;
					break;
			}

			switch ($newRank) {
				case 5:
					$jbp += 1;
					break;
				case 6:
					$bj += 1;
					break;
				case 7:
					$vj += 1;
					break;
			}
		}
		/* update status rank done */

		$erank = ERank::firstOrCreate(['jbid' => $jbid, 'mid' => $mid], ['spid' => $spid, 'upid' => $upid]);
		$current_erank = $erank->erank;
		$erank->ppv -= $ppv;
		$erank->gpv -= $gpvplus;
		$erank->erank = $this->check_eRank($jbid, $erank->ppv, $mid);
		$erank->save();

		if ($upid) $this->update_Effective_Rank($upid, 0, $ppv, $updated_at, $jbp, $bj, $vj);
	}

	public function get_Rank_Name($rank)
	{
		switch ($rank) {
			case 1:
				return "Civilian";
				break;
			case 2:
				return "Joykeeper";
				break;
			case 3:
				return "Joypriser";
				break;
			case 4:
				return "Joypreneur";
				break;
			case 5:
				return "JoyBizPreneur";
				break;
			case 6:
				return "Baron";
				break;
			case 7:
				return "Viscount";
				break;
			case 8:
				return "Earl";
				break;
			case 9:
				return "Marquis";
				break;
			case 10:
				return "Duke";
				break;
			case 11:
				return "Crown";
				break;
			case 12:
				return "Royal Crown";
				break;
		}
	}

	public function get_Rank_Code($rank)
	{
		switch ($rank) {
			case 0:
				return "-";
				break;
			case 1:
				return "C";
				break;
			case 2:
				return "JK";
				break;
			case 3:
				return "JPS";
				break;
			case 4:
				return "JP";
				break;
			case 5:
				return "JBP";
				break;
			case 6:
				return "BJ";
				break;
			case 7:
				return "VJ";
				break;
			case 8:
				return "EJ";
				break;
			case 9:
				return "MJ";
				break;
			case 10:
				return "DJ";
				break;
			case 11:
				return "CA";
				break;
			case 12:
				return "RCA";
				break;
		}
	}

	public function changeNIK($id, $nik)
	{
		$userlogin = Joybiz::initauth();

		$user = User::where("id", $id)->first();

		if ($nik != $user->no_ktp) {

			$user->no_ktp = $nik;
			$user->updated_userid = $userlogin->id;
			$user->save();

			return 'Change NIK Success!!';
		} else {
			return 'NIK sama dengan yang terdaftar!!';
		}
	}


	public function saveImage($filename, $img)
	{

		$data = base64_decode($img);
		file_put_contents($filename, $data);

		if (file_exists($filename)) {
			return true;
		}

		return false;
	}

	//fuction to update transaction if payment valid
	public function confirmPaymentValid($code, $date)
	{
		$trx = transaksi::where('code_trans', $code)->whereIn('status', ['P', 'WP', 'CP'])->with('transaksi_detail')->with('user')->first();
		$indent = false;
		$helper = new \App\AlvaMarvello\Helper;

		if ($trx) {

			foreach ($trx['transaksi_detail'] as $trxd) {
				$product_indent = barang::where([['id', $trxd->id_barang_fk], ['status', 'I']])->with('barang_detail')->first();
				if (isset($product_indent)) $indent = true;

				//jika product joypolinse indent, generate coupon undian
				if ($product_indent) {
					$qty = $product_indent['barang_detail']->where('id_barang_fk', 3)->sum('qty') * $trxd->qty;
					for ($i = 0; $i < $qty; $i++) {
						$helper->GenerateCoupon(1, $trx['user']->uid, $trx->code_trans);
					}
				}
			}

			$trx->transaction_date = $date;
			$trx->status = $indent ? 'I' : 'PC';
			$saved = $trx->save();

			if ($saved) {
				$jbid = $trx->id_cust_fk;

				$ppv = $trx->pv_total;
				$pbv = $trx->bv_total;
				$gpv = 0;
				$gbv = 0;

				$jbp = 0;
				$bj = 0;
				$vj = 0;

				$ppvj = $trx->pv_plan_joy;
				$pbvj = $trx->bv_plan_joy;
				$gpvj = 0;
				$gbvj = 0;

				$ppvb = $trx->pv_plan_biz;
				$pbvb = $trx->bv_plan_biz;
				$gpvb = 0;
				$gbvb = 0;

				$omzet = $trx->purchase_cost;
				$omzet_joy = $trx->price_joy;
				$omzet_biz = $trx->price_biz;

				if ($ppv) $this->update_Effective_Rank_Sim($jbid, $ppv, $pbv, $gpv, $gbv, $date, $jbp, $bj, $vj, $ppvj, $pbvj, $gpvj, $gbvj, $ppvb, $pbvb, $gpvb, $gbvb, $omzet, $omzet_joy, $omzet_biz);
			}

			$status = true;
			$message = "Payment Confirmation Success!!";
		} else {

			$status = false;
			$message = "Transaction not found!!";
		}

		$result = ['status' => $status, 'message' => $message];
		return $result;
	}

	public function addNewShippingAddress(Request $request)
	{
		$input = $request->all();

		//dd($input);

		$code_trans = $input['code_trans'];
		$trx = transaksi::where('code_trans', $code_trans)->first();

		$jbid = $trx->id_cust_fk;
		$name = $input['name'];
		$hp = $input['hp'];
		$address = $input['address'];
		$district = $input['kelurahan'];
		$village = $input['kecamatan'];
		$city = $input['kota_kabupaten'];
		$province = $input['provinsi'];
		$postcode = $input['postcode'];
		$default = isset($input['default']) ? $input['default'] : false;

		if ($default) {
			$affected = UserShippingAddress::where('jbid', $jbid)->update(['default' => false]);
		}

		$result = UserShippingAddress::firstOrCreate([
			'jbid' => $jbid, 'name' => $name, 'hp' => $hp,
			'address' => $address, 'district' => $district, 'village' => $village, 'city' => $city,
			'province' => $province, 'postcode' => $postcode, 'default' => $default
		]);

		$trx->shipping_id = $result->id;
		$trx->save();

		$this->updatePriceByZone($code_trans);

		$data['shipping'] = $result;

		return back();
	}

	//tambah baru alamat pengiriman
	public function addShippingAddress($request)
	{
		$i = $request;

		$district = alamat_kelurahan::where('id', $i['shipping_district'])->first();
		$village = alamat_kecamatan::where('id', $i['shipping_village'])->first();
		$city = alamat_kabupaten::where('id', $i['shipping_city'])->first();
		$province = alamat_provinsi::where('id', $i['shipping_province'])->first();

		$jbid = $i["id_cust_fk"];
		$name = $i['shipping_name'];
		$hp = $i['shipping_phone'];
		$address = $i['shipping_address'];
		$district = $district->kelurahan;
		$village = $village->kecamatan;
		$city = $city->kabupaten;
		$province = $province->provinsi;
		$postcode = $i['postcode'];
		$default = true;
		//$default = isset($input['default']) ? $input['default'] : false;

		if ($default) {
			$affected = UserShippingAddress::where('jbid', $jbid)->update(['default' => false]);
		}

		$result = UserShippingAddress::firstOrCreate([
			'jbid' => $jbid, 'name' => $name, 'hp' => $hp,
			'address' => $address, 'district' => $district, 'village' => $village, 'city' => $city,
			'province' => $province, 'postcode' => $postcode, 'default' => $default
		]);

		$data['shipping'] = $result;

		return $result;
	}

	public function updatePriceByZone($code_trans)
	{
		$trx = transaksi::where('code_trans', $code_trans)->first();
		$trx_detail = transaksi_detail::where('id_trans_fk', $trx->id)->get();
		$shipping = UserShippingAddress::where('id', $trx->shipping_id)->first();
		$zone = alamat_provinsi::select('kelompok')->where('provinsi', $shipping->province)->first();

		//dd($trx_detail);
		foreach ($trx_detail as $td) {
			//dd($td);
			$product = barang::where('id', $td->id_barang_fk)->first();

			//$td->sell_price = $td->qty * $product->{"harga_".$zone->kelompok};
			$td->sell_price = $product->{"harga_" . $zone->kelompok};
			$td->save();
		}

		$total = $trx_detail->sum('sell_price');
		$trx->purchase_cost = $total;
		$trx->save();
	}

	public function convertKTPtoImageFile($img, $id)
	{
		$filename = storage_path('app/public/ktp/' . $id . ".png");

		$file = fopen($targetPath, 'wb');

		// split the string on commas
		// $data[ 0 ] == "data:image/png;base64"
		// $data[ 1 ] == <actual base64 string>
		$data = explode(',', $img);

		// we could add validation here with ensuring count( $data ) > 1
		fwrite($file, base64_decode($data[1]));

		// clean up the file resource
		fclose($file);

		return true;
	}

	public function useVoucher($owner, $amount, $code_trans)
	{
		$joybizz = new Joybiz;
		$userlogin = $joybizz->initauth();
		$username = $userlogin ? $userlogin->username : "API";

		$voucher = \App\Voucher::where('owner', $owner->uid)->first();
		$saldo = decrypt($voucher->saldo);

		$now = Carbon::now();
		$sDate = Carbon::now()->startOfMonth();
		$eDate = $now->endOfMonth();
		$index = \App\VoucherDetail::whereBetween('created_at', [$sDate, $eDate])->count() + 1;

		//dd($owner->username);
		$code = "UVCI" . $now->year . $now->format('m') . $index;
		$note = "Voucher dipakai oleh " . $owner->username . " pada " . $now . " untuk transaksi " . $code_trans . " issued_by " . $username;

		try {
			DB::beginTransaction();
			$result = \App\VoucherDetail::create(['owner' => $owner->uid, 'code' => $code, 'debit' => encrypt(0), 'credit' => encrypt($amount), 'note' => $note]);

			$new_saldo = $saldo - $amount;
			$voucher->saldo = encrypt($new_saldo);
			$voucher->save();
			DB::commit();
		} catch (\PDOException $e) {
			DB::rollBack();
			Log::error($e);
		}

		return $code;
	}

	public static function getSaldoVoucher($member_uuid)
	{
		$voucher = Voucher::where('member_uuid', $member_uuid)->first();
		if ($voucher) return $voucher->saldo;
	}

	public function flatten($array)
	{
		$result = [];
		foreach ($array as $item) {
			if (is_array($item)) {
				$result[] = array_filter($item, function ($array) {
					return !is_array($array);
				});
				$result = array_merge($result, $this->flatten($item));
			}
		}
		return array_filter($result);
	}
}
