<?php

require_once __DIR__ . '/vendor/autoload.php';

use Utopia\App;
use Utopia\Request;
use Utopia\Response;
use Utopia\Validator\Boolean;
use Utopia\Validator\Numeric;
use Utopia\Validator\Text;


    App::get('/hello-world')
        ->inject('request')
        ->inject('response')
        ->action(
            function($request,$response){
                $response
                ->json(['Hello'=>'World']);
            }

        );
    App::get('/students')
    ->inject('request')
    ->inject('response')
    ->action(
        function($request,$response){
            $data=file_get_contents("students.json");
            $data=json_decode($data,true);
            $response
            ->json($data);
        }

    );
    App::post('/students/create-student')
    ->param('student_id',"",new Numeric(),'ID of the student')
    ->param('name',"",new Text(10),'Name of the student')
    ->param('mark',0,new  Text(5),'Mark scored by the student')
    ->param('is_passed',false,new Boolean(),'Exam Status of the student')
    ->inject('response')
    ->action(
        function($student_id,$name,$mark,$is_passed,$response){
          $json=json_decode(file_get_contents('/app/students.json'),true);
          $entry=[
              'student_id'=> $student_id,
              'name'=> $name,
              'mark'=> $mark,
              'is_passed'=> $is_passed
          ];
          array_push($json,$entry);
          $data = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
          file_put_contents('/app/students.json', $data);
          $response->json($json);
        }
    );

    App::put('/students/update-student')
    ->param('student_id',"",new Numeric(),'Id of the student')
    ->param('mark',0,new  Text(5),'Mark scored by the student')
    ->param('is_passed',false,new Boolean(),'Exam Status of the student')
    ->inject('response')
    ->action(
        function($student_id,$mark,$is_passed,$response){
            $students_array=json_decode(file_get_contents('/app/students.json'),true);
            foreach ($students_array as $i){
                if($i->student_id==$student_id){
                    $i->mark=$mark;
                    $i->is_passed=$is_passed;
                    break;
                }
            }
            $data = json_encode($students_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents('/app/students.json',$data);
            $response->json($students_array);
        }
    );

    App::delete('students/delete-student')
    ->param('student_id',"",new Numeric(),'Id of the student')
    ->inject('response')
    ->action(
        function($student_id,$response){
            $students_array=json_decode(file_get_contents('/app/students.json'),true);
            foreach ($students_array as $object =>$i){
                if($i['student_id']===$student_id){
                    unset($students_array[$object]);
                    break;
                }
            }
            $data = json_encode($students_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents('/app/students.json',$data);
            $response->json($students_array);
        }
    );

$app=new App('America/New_York');
$request    = new Request();
$response   = new Response();

$app->run($request, $response);