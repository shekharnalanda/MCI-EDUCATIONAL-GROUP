<?php

namespace App\Console\Commands;

use App\Models\AttendanceBranch;
use App\Models\AttendanceDevice;
use App\Models\Institution;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OnboardCentralBiometricInstitutions extends Command
{
    protected $signature =
        'mci:biometric-onboard
         {--apply : Apply onboarding}
         {--token-file= : Secure one-time token output file}';

    protected $description =
        'Onboard MCI institutions/branches into Central Biometric Attendance';

    public function handle(): int
    {
        $plan = [
            [
                'institution_id' => 1,
                'expected' => 'Micro Computer Institute',
                'branches' => [
                    [
                        'name' => 'Main Campus',
                        'code' => 'MCI-MAIN',
                        'device' => 'MCI-MAIN-IRIS-01',
                    ],
                ],
            ],
            [
                'institution_id' => 2,
                'expected' => 'C-Net Computer Institute',
                'branches' => [
                    [
                        'name' => 'Bihar Sharif',
                        'code' => 'CNETCI-BS',
                        'device' => 'CNETCI-BS-IRIS-01',
                    ],
                ],
            ],
            [
                'institution_id' => 3,
                'expected' => 'C-Net Pathshala',
                'branches' => [
                    [
                        'name' => 'Bihar Sharif',
                        'code' => 'PATH-BS',
                        'device' => 'PATH-BS-IRIS-01',
                    ],
                    [
                        'name' => 'Goraur',
                        'code' => 'PATH-GR',
                        'device' => 'PATH-GR-IRIS-01',
                    ],
                ],
            ],
            [
                'institution_id' => 16,
                'expected' => 'Kushal Youth Programme',
                'branches' => [
                    [
                        'name' => 'Main Attendance Centre',
                        'code' => 'KYP-MAIN',
                        'device' => 'KYP-IRIS-01',
                        'preserve' => true,
                    ],
                ],
            ],
        ];

        $apply = (bool) $this->option('apply');
        $tokenFile = $this->option('token-file');

        if ($apply && ! $tokenFile) {
            throw new RuntimeException(
                '--token-file is required in apply mode.'
            );
        }

        if ($apply) {
            $directory = dirname($tokenFile);

            if (! is_dir($directory)) {
                throw new RuntimeException(
                    'Token output directory does not exist.'
                );
            }

            if (file_exists($tokenFile)) {
                throw new RuntimeException(
                    'Token output file already exists.'
                );
            }
        }

        $stats = [
            'institutions' => 0,
            'branches_created' => 0,
            'branches_existing' => 0,
            'devices_created' => 0,
            'devices_existing' => 0,
            'kyp_preserved' => 0,
        ];

        $newTokens = [];

        DB::transaction(function () use (
            $plan,
            $apply,
            &$stats,
            &$newTokens
        ) {
            foreach ($plan as $entry) {
                $institution = Institution::find(
                    $entry['institution_id']
                );

                if (! $institution) {
                    throw new RuntimeException(
                        'Institution missing: '.
                        $entry['institution_id']
                    );
                }

                if (
                    strcasecmp(
                        trim($institution->name),
                        trim($entry['expected'])
                    ) !== 0
                ) {
                    throw new RuntimeException(
                        'Institution identity mismatch for ID '.
                        $entry['institution_id'].
                        ': '.$institution->name
                    );
                }

                $stats['institutions']++;

                $this->line(
                    'INSTITUTION='.
                    $institution->id.
                    ' | '.$institution->name
                );

                foreach ($entry['branches'] as $item) {
                    $branch = AttendanceBranch::query()
                        ->where(
                            'institution_id',
                            $institution->id
                        )
                        ->where('code', $item['code'])
                        ->first();

                    if ($branch) {
                        $stats['branches_existing']++;
                    } else {
                        $stats['branches_created']++;

                        if ($apply) {
                            $branch = new AttendanceBranch();
                            $branch->institution_id =
                                $institution->id;
                            $branch->code =
                                $item['code'];
                        }
                    }

                    if (! $apply) {
                        $this->line(
                            '  BRANCH='.$item['code'].
                            ' DEVICE='.$item['device'].
                            ' MODE=PLAN'
                        );
                        continue;
                    }

                    $branch->name = $item['name'];
                    $branch->is_active = true;

                    $branch->metadata = array_merge(
                        (array) ($branch->metadata ?? []),
                        [
                            'central_biometric' => true,
                            'onboarded_by' =>
                                'mci:biometric-onboard',
                        ]
                    );

                    $branch->save();

                    $device = AttendanceDevice::query()
                        ->where(
                            'device_code',
                            $item['device']
                        )
                        ->first();

                    if ($device) {
                        $stats['devices_existing']++;

                        if (! empty($item['preserve'])) {
                            $stats['kyp_preserved']++;
                        }
                    } else {
                        if (! empty($item['preserve'])) {
                            throw new RuntimeException(
                                'Existing KYP device missing; refusing replacement.'
                            );
                        }

                        $plainToken =
                            'mci_iris_'.Str::random(56);

                        $device = new AttendanceDevice();

                        $device->device_code =
                            $item['device'];

                        /*
                         * API authentication uses token_hash.
                         * Plain token is NEVER stored in DB.
                         */
                        $device->token_hash =
                            hash('sha256', $plainToken);

                        $newTokens[] = [
                            'institution' =>
                                $institution->name,
                            'branch' =>
                                $branch->name,
                            'device_code' =>
                                $device->device_code,
                            'device_token' =>
                                $plainToken,
                        ];

                        $stats['devices_created']++;
                    }

                    $device->institution_id =
                        $institution->id;

                    $device->attendance_branch_id =
                        $branch->id;

                    $device->name =
                        $institution->name.
                        ' - '.
                        $branch->name.
                        ' MIS100V2';

                    $device->location =
                        $branch->name;

                    $device->is_active = true;

                    $device->metadata = array_merge(
                        (array) ($device->metadata ?? []),
                        [
                            'device_model' =>
                                'Mantra MIS100V2',
                            'architecture' =>
                                'Windows x86',
                            'central_connector' =>
                                true,
                        ]
                    );

                    $device->save();

                    $this->line(
                        '  BRANCH='.$branch->code.
                        ' DEVICE='.$device->device_code.
                        ' STATUS=READY'
                    );
                }
            }
        });

        if ($apply) {
            $lines = [
                'MCI CENTRAL BIOMETRIC - NEW DEVICE CREDENTIALS',
                'Generated: '.now()->toDateTimeString(),
                '',
                'KEEP THIS FILE PRIVATE.',
                'Tokens are shown here only for initial connector configuration.',
                '',
            ];

            foreach ($newTokens as $row) {
                $lines[] =
                    'Institution: '.$row['institution'];
                $lines[] =
                    'Branch: '.$row['branch'];
                $lines[] =
                    'Device Code: '.$row['device_code'];
                $lines[] =
                    'Device Token: '.$row['device_token'];
                $lines[] = str_repeat('-', 60);
            }

            file_put_contents(
                $this->option('token-file'),
                implode(PHP_EOL, $lines).PHP_EOL,
                LOCK_EX
            );

            chmod(
                $this->option('token-file'),
                0600
            );
        }

        foreach ($stats as $key => $value) {
            $this->line(
                strtoupper($key).'='.$value
            );
        }

        $this->info(
            'CENTRAL_BIOMETRIC_ONBOARDING=PASS'
        );

        return self::SUCCESS;
    }
}
