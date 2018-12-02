<?php

	/*
		Update Script by Cain Kilgore.
		Modify the values below with your own API Key, Zone ID etc which you can easily
		get from CloudFlare. Last modified: 2-12-2018
	*/

	$email   = "hello@world.me";
	$api_key = "<API KEY>";
	$zone_id = "<ZONE ID>";

	$cf_base = "https://api.cloudflare.com/client/v4";
	$domain = "<A RECORD TO UPDATE>";

	$curl_header = ["Content-Type: application/json", "X-Auth-Key: $api_key", "X-Auth-Email: $email"];

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => "https://ip.cain.sh",
		CURLOPT_HTTPHEADER => $curl_header,
		CURLOPT_USERAGENT => "curl"
	));
	$current_ip = curl_exec($curl);
	$current_ip = preg_replace('~[\r\n]+~', '', $current_ip);
	print_r("Your current WAN IP is: $current_ip\n");

	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => "$cf_base/zones/$zone_id/dns_records?type=A&name=$domain",
		CURLOPT_HTTPHEADER => $curl_header
	));

	$res = curl_exec($curl);
	$recordId = @json_decode($res)->result[0]->id;

	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => "$cf_base/zones/$zone_id/dns_records/$recordId",
		CURLOPT_CUSTOMREQUEST => "PUT",
		CURLOPT_POSTFIELDS => json_encode(array("type" => "A", "name" => $domain, "content" => $current_ip)),
	));

	$res = curl_exec($curl);
	if(json_decode($res)->success) {
		print_r("Updated successfully. $domain is now pointing to $current_ip.\n");
	} else {
		print_r("There was an error updating the record. The error is detailed below.\n");
		print_r($res);
		print_r("\n");
	}
?>
