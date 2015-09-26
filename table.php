<?php

$projects = Array(1420598,1425026, 1431288, 1431204);
class Worker{
    var $PIVO_SERVICE_URL = "/services/v5/projects/";
    var $projects;
    var $cdata;
    var $tdata;
    var $row_data = array (
        'name' => "/?fields=name",
        'last_update' => "/activity?limit=1&offset=0&fields=message,occurred_at",
        'last_commit' => "/activity?limit=20&fields=kind,changes",
        'delivered' => "/stories?with_state=delivered&fields=id",
    );

    function Worker($projects){
        $this->projects = $projects;
        $this->cdata=$this->get_curl_data();
    }

    function get_url($pid,$endpoint){
        return $this->PIVO_SERVICE_URL.$pid.$endpoint;
    }

    function make_urls() {
        $pages = array();
        foreach($this->projects as $pid) {
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
        array_push($row, $data->name);

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
        foreach($this->projects as $pid){
            array_push($tdata,$this->get_row($pid));
        }
        return $tdata;
    }
}

$rows = new worker($projects);
$pealkiri = Array("Projekti nimi", "Viimase  kande pealkiri", "Aeg", "Commiti message", "Aeg", "Acceptimata storyd");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pivotal_tracker tabelina</title>
</head>
<body>

<table border=1>

    <thead>

    <?php foreach ($pealkiri as &$p): ?>
        <th><?= $p ?></th>
    <? endforeach ?>

    </thead>

    <?php foreach ($rows->get_table_data() as $row): ?>
        <tr>
            <? foreach ($row as &$column): ?>

                <td><?= $column ?></td>

            <? endforeach ?>
        </tr>

    <? endforeach ?>
</table>

</body>
</html>
