<?php

//$projects = Array(1437300);
$projects = Array();
$file = fopen("VS15 Projektid - Pseudoülesannete projekt.csv","r");

while(! feof($file))
//FIXME:csv file withot read premission gives endless loop.
      {
          $line = fgetcsv($file);
          if (stristr($line[2], 'pivotaltracker.com/n/projects/', false) ){
              $p_link = explode("/",$line[2]);
              $projects[$p_link[5]]=$line[0];
              //$projects[]=
          }
              }
fclose($file);

class Worker{
    var $PIVO_SERVICE_URL = "/services/v5/projects/";
    var $projects;
    var $pids;
    var $cdata;
    var $tdata;
    var $with_error= Array();
    var $row_data = array (
        'name' => "/?fields=name",
        'last_update' => "/activity?limit=1&offset=0&fields=message,occurred_at",
        'last_commit' => "/activity?limit=20&fields=kind,changes",
        'delivered' => "/stories?with_state=delivered&fields=id",
    );

    function Worker($projects){
        $this->projects = $projects;
        $this->pids = array_keys($projects);
        $this->cdata=$this->get_curl_data();
    }

    function get_url($pid,$endpoint){
        return $this->PIVO_SERVICE_URL.$pid.$endpoint;
    }

    function make_urls() {
        $pages = array();
        foreach($this->pids as $pid) {
            foreach($this->row_data as $k => $endpoint){
                array_push($pages, $this->get_url($pid, $endpoint));
            }
        }
        return json_encode($pages);

    }

    function get_curl_data(){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.pivotaltracker.com/services/v5/aggregator",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $this->make_urls(),
        ));

        $response = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            //echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    function get_data($pid, $endpoint) {
        $url=$this->get_url($pid, $this->row_data[$endpoint]);
        return $this->cdata->$url;
    }


    function get_row($pid){
        $row = Array();
        //Projekti nimi
        $data = $this->get_data($pid, 'name');
        if ( array_key_exists('kind', $data)) {
            $this->with_error[]=$this->projects[$pid];
            return $row;
        }else {
            $name = '<a href=https://www.pivotaltracker.com/n/projects/'.$data->id.'>'.$data->name.'</a>';
            array_push($row, $name);
        };
        //Viimase uuenduse pealkiri ja aeg
        $data = $this->get_data($pid, 'last_update');
        array_push($row, $data[0]->message);
        array_push($row, $data[0]->occurred_at);

        //Viimane commit ja aeg
        $data = $this->get_data($pid, 'last_commit');
        $no_commits=True;
        foreach ($data as &$act){
            if($act->kind == "comment_create_activity" && array_key_exists("commit_type", $act->changes[0]->new_values)){
                array_push($row, $act->changes[0]->new_values->text);
                array_push($row, $act->changes[0]->new_values->updated_at);
                $no_commits=False;
                break;
            }
        }
        if ($no_commits){
            array_push($row, "-");
            array_push($row, "-");
        }

        //Acceptimata storyd
        $data = $this->get_data($pid, 'delivered');
        array_push($row, count($data));

        return $row;

    }

    public function get_table_data(){
        $tdata = array();
        foreach($this->pids as $pid){
            array_push($tdata,$this->get_row($pid));
        }
        return $tdata;
    }

    public function get_with_error(){
        return $this->with_error;
    }
}

$pivo_staatus = new worker($projects);
$status_table = $pivo_staatus->get_table_data();
$with_error = $pivo_staatus->get_with_error();
$pealkiri = Array("Projekti nimi", "Viimase  kande pealkiri", "Aeg", "Commiti message", "Aeg", "Acceptimata storyd");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pivotal_tracker tabelina</title>
</head>
<body>

<?php if($with_error): ?>
    <h2>Järgmiste isikute Pivotal Trackeri projekti lugemisega tekkis probleem:</h2>
    <ol>
        <?php foreach ($with_error as $el): ?>
            <li><?= $el ?></li>
        <? endforeach ?>
    </ol>
<?php endif ?>

<table border=1>

    <thead>

    <?php foreach ($pealkiri as &$p): ?>
        <th><?= $p ?></th>
    <? endforeach ?>

    </thead>

    <?php foreach ($status_table as $row): ?>
        <tr>
            <? foreach ($row as &$column): ?>

                <td><?= $column ?></td>

            <? endforeach ?>
        </tr>

    <? endforeach ?>
</table>

</body>
</html>
