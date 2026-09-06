<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IrisSoftwareController extends Controller
{
    private function directory(): string
    {
        return storage_path('app/private/iris-software');
    }

    public function index()
    {
        $connector = $this->directory().
            '/MCI-Biometric-Connector-Windows-x86.zip';

        return view('admin.iris-software', [
            'connectorAvailable' => is_file($connector),
            'connectorSize' => is_file($connector)
                ? $this->formatBytes(filesize($connector))
                : null,

            'driverAvailable' => is_file(
                $this->directory().'/Mantra-MIS100V2-Driver.zip'
            ),

            'runtimeAvailable' => is_file(
                $this->directory().'/MIS100V2-Windows-Runtime.zip'
            ),
        ]);
    }

    public function download(
        Request $request,
        string $package
    ): BinaryFileResponse {
        $packages = [
            'connector' =>
                'MCI-Biometric-Connector-Windows-x86.zip',

            'driver' =>
                'Mantra-MIS100V2-Driver.zip',

            'runtime' =>
                'MIS100V2-Windows-Runtime.zip',
        ];

        abort_unless(
            array_key_exists($package, $packages),
            404
        );

        $file = $this->directory().
            '/'.$packages[$package];

        abort_unless(is_file($file), 404);

        return response()->download(
            $file,
            $packages[$package],
            [
                'Cache-Control' =>
                    'private, no-store, max-age=0',
                'X-Content-Type-Options' =>
                    'nosniff',
            ]
        );
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format(
                $bytes / 1048576,
                2
            ).' MB';
        }

        return number_format(
            $bytes / 1024,
            1
        ).' KB';
    }
}
