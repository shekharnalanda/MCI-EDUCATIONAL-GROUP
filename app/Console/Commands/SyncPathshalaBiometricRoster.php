<?php

namespace App\Console\Commands;

use App\Models\AttendanceBranch;
use App\Models\AttendanceStudent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;

class SyncPathshalaBiometricRoster extends Command
{
    protected $signature='mci:sync-pathshala-biometric';

    protected $description=
        'Automatically sync real active C-Net Pathshala students into Central Biometric roster';

    public function handle(): int
    {
        $envFile='/home4/mcied45x/repositories/senet-pathshala-live/.env';

        if (!is_file($envFile)) {
            throw new RuntimeException('Pathshala environment unavailable.');
        }

        $env=[];

        foreach(file($envFile,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){
            if(str_starts_with(trim($line),'#') || !str_contains($line,'=')) continue;
            [$k,$v]=explode('=',$line,2);
            $env[trim($k)]=trim(trim($v),"'\"");
        }

        $pdo=new PDO(
            'mysql:host='.($env['DB_HOST']??'127.0.0.1').
            ';port='.($env['DB_PORT']??'3306').
            ';dbname='.$env['DB_DATABASE'].
            ';charset=utf8mb4',
            $env['DB_USERNAME'],
            $env['DB_PASSWORD'],
            [
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            ]
        );

        $sql="
            SELECT
                s.id,
                s.branch_id,
                s.admission_no,
                s.roll_no,
                s.class_name,
                s.section,
                s.guardian_phone,
                s.photo,
                u.name
            FROM students s
            INNER JOIN users u ON u.id=s.user_id
            WHERE s.is_active=1
              AND s.admission_no NOT LIKE 'DEMO-%'
            ORDER BY s.branch_id,s.id
        ";

        $rows=$pdo->query($sql)->fetchAll();

        $branchMap=[
            1=>AttendanceBranch::where('code','PATH-BS')->firstOrFail(),
            2=>AttendanceBranch::where('code','PATH-GR')->firstOrFail(),
        ];

        $seen=[];
        $created=0;
        $updated=0;

        DB::transaction(function() use(
            $rows,$branchMap,&$seen,&$created,&$updated
        ){
            foreach($rows as $row){
                $sourceId=(string)$row['id'];
                $sourceBranch=(int)$row['branch_id'];

                if(!isset($branchMap[$sourceBranch])) {
                    throw new RuntimeException(
                        'Unknown Pathshala branch '.$sourceBranch
                    );
                }

                $branch=$branchMap[$sourceBranch];

                $person=AttendanceStudent::query()
                    ->where('institution_id',3)
                    ->where('metadata->source_system','CNET_PATHSHALA')
                    ->where('metadata->source_id',$sourceId)
                    ->first();

                if(!$person){
                    $person=AttendanceStudent::query()
                        ->where('institution_id',3)
                        ->where('admission_number',$row['admission_no'])
                        ->first();
                }

                if(!$person){
                    $person=new AttendanceStudent();
                    $person->institution_id=3;
                    $person->attendance_code=(string)Str::uuid();
                    $created++;
                } else {
                    $updated++;
                }

                $person->attendance_branch_id=$branch->id;
                $person->person_type='student';
                $person->admission_number=$row['admission_no'];
                $person->roll_number=$row['roll_no'] ?: null;
                $person->name=$row['name'];
                $person->course_class=$row['class_name'] ?: null;
                $person->batch_section=$row['section'] ?: null;
                $person->mobile=$row['guardian_phone'] ?: null;
                $person->status='active';

                $person->metadata=array_merge(
                    (array)($person->metadata ?? []),
                    [
                        'source_system'=>'CNET_PATHSHALA',
                        'source_id'=>$sourceId,
                        'source_branch_id'=>$sourceBranch,
                        'source_photo_path'=>$row['photo'] ?: null,
                        'synced_at'=>now()->toIso8601String(),
                    ]
                );

                $person->save();
                $seen[]=$sourceId;
            }

            /*
             * A previously synced person disappearing from the
             * eligible source roster is deactivated, never deleted.
             * Existing iris/audit history remains intact.
             */
            $query=AttendanceStudent::query()
                ->where('institution_id',3)
                ->where('metadata->source_system','CNET_PATHSHALA');

            if($seen){
                $query->whereNotIn('metadata->source_id',$seen);
            }

            $query->update(['status'=>'inactive']);
        });

        $this->info('SOURCE_REAL='.count($rows));
        $this->info('CREATED='.$created);
        $this->info('UPDATED='.$updated);
        $this->info('PATHSHALA_AUTO_ROSTER_SYNC=PASS');

        return self::SUCCESS;
    }
}
