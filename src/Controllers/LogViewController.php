<?php

namespace Colbeh\Logs\Controllers;

class LogViewController {



	public function index() {
		$this->checkPermission();
		return view("logsViews::index");

	}


	public function filesList() {

		$this->checkPermission();
		$path = request('path', '');

		$fullPath = storage_path('logs/dailylogs') . "/" . $path;
		$files = scandir($fullPath);
		unset($files[0]);
		unset($files[1]);


		$maxSize = 0;

		$filesArray = [];
		foreach ($files as $file) {
			$a["name"] = $file;
			$a["size"] = ($this->dirSize2($fullPath . '/', $file));
			$a["size_text"] = $this->sizePrettifier($a["size"]);
			$a["is_dir"] = is_dir($fullPath . '/' . $file) ? true : false;

			if ($maxSize < $a["size"])
				$maxSize = $a["size"];

			$filesArray[] = $a;
		}

		$data = json_encode(['files' => $filesArray, 'maxSize' => $maxSize, 'path' => $path]);

		return $data;
	}

	/**
	 * Get the directory size
	 *
	 * @param directory $directory
	 *
	 * @return integer
	 */
	private function dirSize2($path, $directory) {
		$size = 0;

		$fullPath = $path . $directory;

		if (is_dir($fullPath)) {
			$files = scandir($fullPath);
			unset($files[0]);
			unset($files[1]);

			foreach ($files as $file) {
				$size += filesize($fullPath . "/" . $file);
			}
		} else {
			$size = filesize($fullPath);
		}

		return $size;
	}


	private function sizePrettifier($size) {
		if ($size < 1024) {
			return number_format($size, 2) . " Byte ";
		} elseif ($size < 1024 * 1024) {
			return number_format($size / 1024, 2) . " KB ";
		} elseif ($size < 1024 * 1024 * 1024) {
			return number_format($size / (1024 * 1024), 2) . " MB ";
		} else  {
			return number_format($size / (1024 * 1024 * 1024), 2) . " GB ";
		}
	}



	public function destroy() {

		$this->checkPermission();
		$path = request('path');
		$name = request('name');
		$fullPath = storage_path('logs/dailylogs') . '/' . $path . '/' . $name;

		if (is_dir($fullPath))
			$this->deleteDirectory($fullPath);
		else
			unlink($fullPath);

		return ["result" => "success"];
	}


	private function deleteDirectory($filePath) {
		$objects = scandir($filePath);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (is_dir($filePath . '/' . $object))
					$this->deleteDirectory($filePath . '/' . $object);
				else
					unlink($filePath . '/' . $object);
			}
		}
		rmdir($filePath);

		return;
	}


	public function show($path1, $path2 = null, $path3 = null) {

		$this->checkPermission();
		$fullPath = $path1;
		if ($path2 != null)
			$fullPath .= "/$path2";

		if ($path3 != null)
			$fullPath .= "/$path3";

		return view('logsViews::show', compact('fullPath'));
	}


	public function showAjax() {
		$this->checkPermission();

		$fullPath = trim(request('path'));
		$search = trim(request('search'));
		$time = trim(request('time'));
		$showResponse = request('show_response');
		$noPaginate = request('no_paginate');
		$justErrors = request('just_errors');
		$sortDuration = request('sort_duration');
		$loadedCount = request('loaded_count');
		$perPage = 10;

		$fullAddress = storage_path('logs/dailylogs') . "/$fullPath";


		// add [ again to first of rows
		$file = file_get_contents($fullAddress);
		$requests = explode("\n\n[", $file);
		for ($i = 1; $i < sizeof($requests); $i++) {
			$requests[$i] = '[' . $requests[$i];
		}

		// search for $search
		$requestsFilter1 = [];
		if ($search != "") {
			foreach ($requests AS $req) {
				if (strpos($req, $search) !== false) {
					$requestsFilter1[] = $req;
				}
			}
		} else {
			$requestsFilter1 = $requests;
		}
		unset($requests);
		$requestsFilter1 = array_values($requestsFilter1);


		// search for $time
		$requestsFilter2 = [];
		if ($time != "") {
			foreach ($requestsFilter1 AS $req) {
				if (strpos($req, "[$time") !== false) {
					$requestsFilter2[] = $req;
				}
			}
		} else {
			$requestsFilter2 = $requestsFilter1;
		}
		unset($requestsFilter1);
		$requestsFilter2 = array_values($requestsFilter2);

		// search for errors
		$requestsFilter3 = [];
		if ($justErrors == "true") {
			foreach ($requestsFilter2 AS $req) {
				if (strpos($req, "Error: ") !== false) {
					$requestsFilter3[] = $req;
				}
			}
		} else {
			$requestsFilter3 = $requestsFilter2;
		}
		unset($requestsFilter2);
		$requestsFilter3 = array_values($requestsFilter3);


		$requestsFilter3 = $this->normalizeRequests($requestsFilter3);
//	return $requestsFilter2;

		// hide responses if $showResponse is false
		$requestsFilter4 = [];
		if ($showResponse == 'false') {
			foreach ($requestsFilter3 AS $req) {
				unset($req['response']);
				$requestsFilter4[] = $req;
			}
		} else {
			$requestsFilter4 = $requestsFilter3;
		}
		unset($requestsFilter3);
		$requestsFilter4 = array_values($requestsFilter4);


		if ($sortDuration == 'true')
			$requestsFilter4 = $this->sortDuration($requestsFilter4);


		$finalRequests = $requestsFilter4;
		unset($requestsFilter4);


		// pagination if needed
		if ($noPaginate == 'false') {
			$finalRequests = array_slice($finalRequests, $loadedCount, $perPage, true);
			$finalRequests = array_values($finalRequests);
		}

		$data = json_encode(['requests' => $finalRequests, 'fullPath' => $fullPath]);

		return $data;
	}


	private function normalizeRequests($requests) {

		$reqList = [];

		foreach ($requests AS $req) {
			$r = [];
			$r['time'] = substr($req, 1, 8);
			$r['ip'] = substr($req, $this->getPosition($req, 'ip: '), $this->getPosition($req, " \t duration: ", false) - $this->getPosition($req, 'ip: '));
			$r['duration'] = substr($req, $this->getPosition($req, " \t duration: "), $this->getPosition($req, " \t url:", false) - $this->getPosition($req, " \t duration: "));
			$r['url'] = substr($req, $this->getPosition($req, " \t url:"), $this->getPosition($req, ",\n request: ", false) - $this->getPosition($req, " \t url:"));
			$r['request'] = substr($req, $this->getPosition($req, ",\n request: "), $this->getPosition($req, ' ,token: ', false) - $this->getPosition($req, ",\n request: "));
			$r['token'] = substr($req, $this->getPosition($req, ' ,token: '), $this->getPosition($req, ",\n response: ", false) - $this->getPosition($req, ' ,token: '));
			$r['response'] = substr($req, $this->getPosition($req, "response: "));
			$r['error'] = strpos($req, "Error: ") != false;

			$reqList[] = $r;
		}

		return $reqList;
	}


	private function getPosition($string, $search, $endPos = true) {
		$pos = strpos($string, $search);
		if ($endPos)
			$pos += strlen($search);

		return $pos;
	}


	private function sortDuration($requests) {
		function compare($a, $b) {
			return -strcmp($a["duration"], $b["duration"]);
		}

		usort($requests, function ($a, $b) {
			return -strcmp($a["duration"], $b["duration"]);
		});
		return $requests;
	}


	private function checkPermission() {
		if (auth('web')->user() == null)
			abort(403);

		auth()->user()->authorizeRoles('superAdmin');

	}

}



