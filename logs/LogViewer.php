<?php


if(isset($_GET['del'])){
    $f = base64_decode($_GET['del']);
    $path = "../application/logs";
    if(file_exists($path.'/'.$f)){
        @unlink($path.'/'.$f);
        header("Refresh:0; url=LogViewer.php");
    }
}


$files = array();
$logs = array();


$path = "../application/logs";


if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {

    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $files[] = $entry;
        }
    }
    closedir($handle);
}


function getLog($file){

    $path = "../application/logs";

    $file = $path.'/' . $file;

    if (file_exists($file)) {

        $size = filesize($file);

        if ($size >= 5242880) {
            $suffix = array(
                'B',
                'KB',
                'MB',
                'GB',
                'TB',
                'PB',
                'EB',
                'ZB',
                'YB'
            );

            $i = 0;

            while (($size / 1024) > 1) {
                $size = $size / 1024;
                $i++;
            }

            $error_warning = 'Warning: Your error log file %s is %s!';

            $data['error_warning'] = sprintf($error_warning, basename($file), round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
        } else {

            // Updated from comment

            $log = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
            $lines = explode("ERROR", $log);
            $content = implode("\n", array_slice($lines, 1));
            //$lines = explode("\n", $content);

            $errors = array();

            foreach ($lines as $k => $line){
                if( (trim($line) && trim($line) =="") OR !preg_match("#-->#",$line) ){
                    unset($lines[$k]);
                }else{
                    $l = explode("-->",$line);

                    if(count($l)>2){
                        $error = array(
                            'date_level' => $l[0],
                            'content' => $l[2],
                        );
                    }else{
                        $error = array(
                            'date_level' => $l[0],
                            'content' => $l[1],
                        );
                    }

                    $errors[] = $error;
                }
            }

            return $errors;
        }
    }

    return array();
}

$currentFile = "log-".date("Y-m-d").".php";

if(isset($_GET['f'])){
    $f = base64_decode($_GET['f']);
    $path = "../application/logs";
    if(file_exists($path.'/'.$f)){
        $currentFile = $f;
    }
}

$logs = getLog($currentFile);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logs viewer</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.css">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        body {
            padding: 25px;
        }

        h1 {
            font-size: 1.5em;
            margin-top: 0;
        }

        .date {
            min-width: 75px;
        }

        .text {
            word-break: break-all;
        }

        a.llv-active {
            z-index: 2;
            background-color: #f5f5f5;
            border-color: #777;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <h1><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> DT Log Viewer</h1>
            <p class="text-muted"><i>by <a href="#" target="_blank">DT team</a></i></p>
            <div class="list-group">
            <?php if(empty($files)): ?>
                    <a class="list-group-item liv-active">No Log Files Found</a>
            <?php else: ?>
                <?php foreach($files as $file): ?>
                        <a href="?f=<?= base64_encode($file); ?>"
                           class="list-group-item <?= ($currentFile == $file) ? "llv-active" : "" ?>">
                            <?= $file; ?>
                        </a>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-9 col-md-10 table-container">
        <?php if(is_null($logs)): ?>
                <div>
                    <br><br>
                    <strong>Log file > 50MB, please download it.</strong>
                    <br><br>
                </div>
        <?php else: ?>
                <table id="table-log" class="table table-striped">
                    <thead>
                    <tr>
                        <th width="20%">Level & Date</th>
                        <th width="80%">Content</th>
                    </tr>
                    </thead>
                    <tbody>

                <?php foreach($logs as $key => $log): ?>
                        <tr data-display="stack<?= $key; ?>">

                            <td><?=$log['date_level']?></td>
                            <td><?=$log['content']?></td>

                        </tr>
                <?php endforeach; ?>
                    </tbody>
                </table>
        <?php endif; ?>
            <div>
            <?php if($currentFile): ?>
                    <a id="delete-log" href="?del=<?= base64_encode($currentFile); ?>"><span
                            class="glyphicon glyphicon-trash"></span> Delete file</a>

            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<script>
    $(document).ready(function () {

        $('.table-container tr').on('click', function () {
            $('#' + $(this).data('display')).toggle();
        });

        $('#table-log').DataTable({
            "order": [],
            "stateSave": true,
            "stateSaveCallback": function (settings, data) {
                window.localStorage.setItem("datatable", JSON.stringify(data));
            },
            "stateLoadCallback": function (settings) {
                var data = JSON.parse(window.localStorage.getItem("datatable"));
                if (data) data.start = 0;
                return data;
            }
        });
        $('#delete-log, #delete-all-log').click(function () {
            return confirm('Are you sure?');
        });
    });
</script>
</body>
</html>
