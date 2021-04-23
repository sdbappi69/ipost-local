<?php

	function iPostStatus($sub_order_status){

		switch ($sub_order_status) {
			case '1':
			case '2':
				$status = 1;
				break;

			case '3':
				$status = 2;
				break;

			case '4':
                        case '5':
				$status = 3;
				break;

			case '6':
			case '7':
			case '8':
			case '9':
				$status = 4;
				break;

			case '10':
			case '11':
			case '12':
			case '13':
			case '14':
			case '15':
			case '16':
			case '17':
				$status = 5;
				break;

			case '18':
			case '19':
			case '20':
			case '21':
				$status = 6;
				break;

			case '22':
			case '23':
			case '24':
			case '25':
			case '26':
			case '27':
			case '34':
			case '47':
                        case '49': // post delivery order return to buyer
				$status = 7;
				break;

			case '28':
			case '35':
				$status = 8;
				break;

			case '29':
			case '30':
			case '36':
				$status = 9;
				break;

			case '31':
			case '32':
			case '33':
			case '37':
			case '40':
				$status = 10;
				break;

			case '38':
			case '39':
			case '41':
			case '42':
			case '43':
			case '44':
			case '45':
			case '46':
			case '48':
				$status = 11;
				break;
			
			default:
				$status = 1;
				break;
		}

		return $status;

	}


	