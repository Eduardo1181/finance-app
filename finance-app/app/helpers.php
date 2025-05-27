<?php

if (!function_exists('vite_asset')) {
	function vite_asset(string $path): string
	{
		static $manifest;

		if (!$manifest) {
			$manifestPath = public_path('build/manifest.json');
			if (!file_exists($manifestPath)) {
				throw new Exception('Vite manifest not found. Did you run npm run build?');
			}
			$manifest = json_decode(file_get_contents($manifestPath), true);
		}

		if (!isset($manifest[$path])) {
			throw new Exception("Vite asset not found in manifest: {$path}");
		}

		return asset('build/' . $manifest[$path]['file']);
	}
}

if (!function_exists('getCardColorClass')) {
    function getCardColorClass(string $type): string
    {
        return match (strtolower($type)) {
          'black'   => 'card-black',
          'gold'    => 'card-gold',
          'diamond' => 'card-diamond',
          default   => 'card-normal',
        };
    }
}